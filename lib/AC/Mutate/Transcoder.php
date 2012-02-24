<?php

namespace AC\Mutate;

class Transcoder {
	
	/**
	 * The general version for the library, stored as a constant here as this object is the main entry point.
	 */
	const VERSION = "0.6.0";
	
	/**
	 * If a file already exists, remove the pre-existing file before initiating the transcode
	 */
	const ONCONFLICT_DELETE = 1;

	/**
	 * If a file already exists, throw an exception.
	 */
	const ONCONFLICT_EXCEPTION = 2;

	/**
	 * If file already exists, create a derivative file path with numerical increment to avoid conflicts.
	 */
	const ONCONFLICT_INCREMENT = 3;
	
	/**
	 * If a transcode process fails, delete any newly created files
	 */
	const ONFAIL_DELETE = 1;

	/**
	 * If the transcode process fails, keep any created files
	 */
	const ONFAIL_PRESERVE = 2;
	
	/**
	 * If the transcode requires creating a directory, do it, but only for one level
	 */
	const ONDIR_CREATE = 1;
	
	/**
	 * If the transcode requires creating a directory, fail with exception
	 */
	const ONDIR_EXCEPTION = 2;
	
	/**
	 * The octal file creation mode to set for any files created during a transcode process
	 *
	 * @var octal
	 */
	protected $fileCreationMode = 0644;
	
	/**
	 * The octal directory creation mode to set for any directories created during the transcode process
	 *
	 * @var octal
	 */
	protected $directoryCreationMode = 0755;

	/**
	 * Storage array of registered adapters
	 *
	 * Format is hash of adapter_name => object
	 *
	 * @var array
	 */
	protected $adapters = array();
	
	/**
	 * Storage array of registered presets
	 *
	 * Format is hash of preset_name => object
	 *
	 * @var array
	 */
	protected $presets = array();
	
	/**
	 * Storage array of registered jobs.
	 *
	 * Format is hash of job_name => object
	 *
	 * @var array
	 */
	protected $jobs = array();
	
	/**
	 * Array of registered TranscodeEventListeners
	 *
	 * @var array
	 */
	protected $listeners = array();


	/**
	 * The core method of the transcode process.  Takes file input, validates, runs a transcode process, validates return, and returns file output.
	 *
	 * @param mixed $inFile - if a string filepath is given instead of an instance of \AC\Mutate\File, then a new File instance will be created automatically
	 * @param mixed $preset - if a string is given instead of an instance of \AC\Mutate\Preset, then it will look for a Preset with a name matching the received string
	 * @param string $outFile - an optional output file path, even if provided explicity, the Transcoder will validate and process it before starting a transcode process
	 * @param string $conflictMode - flag for what to do if an output file already exists at the given output path
	 * @param string $failMode - flag for what to do with the output file(s) on a failed transcode
	 * @return File - \AC\Mutate\File instance for newly created file
	 */
	public function transcodeWithPreset($inFile, $preset, $outFile = false, $conflictMode = self::ONCONFLICT_INCREMENT, $dirMode = self::ONDIR_EXCEPTION, $failMode = self::ONFAIL_DELETE) {

		//get file
		if(!$inFile instanceof File && is_string($inFile)) {
			$inFile = new File($inFile);
		}
		
		//get preset
		if(!$preset instanceof Preset) {
			$preset = $this->getPreset($preset);
		}
				
		//have preset validate file
		$preset->validateInputFile($inFile);

		//get adapter
		$adapter = $this->getAdapter($preset->getRequiredAdapter());
		
		//verify if this adapter can work in the current environment (happens only the first time it's loaded)
		if(!$adapter->verify()) {
			throw new \RuntimeException($adapter->getVerificationError());
		}
		
		//have adapter verify inputs
		$adapter->validateInputFile($inFile);
		$adapter->validatePreset($preset);
		
		//generate the final output string
		$outFilePath = $preset->generateOutputPath($inFile, $outFile);
		
		//make sure the output path is valid, create any directories as necessary
		$outFilePath = $this->processOutputFilepath($outFilePath, $conflictMode, $dirMode);

		try {
			//TODO: setup some type of logging
			
			//notify listeners of transcode start
			$this->dispatch('onTranscodeStart', $inFile, $preset, $outFilePath);

			//run the transcode
			$return = $adapter->transcodeFile($inFile, $preset, $outFilePath);

			//validate return
			if(!$return instanceof File) {
				throw new Exception\InvalidOutputException("Adapters must return an instance of AC\Mutate\File, or throw an exception upon error.");
			}
			$preset->validateOutputFile($return);
			$adapter->validateOutputFile($return);
			$this->cleanOutputFile($return);

			//notify listeners of completion
			$this->dispatch('onTranscodeComplete', $inFile, $preset, $return);
		
			//return newly created file
			return $return;
		} catch (\Exception $e) {
			//clean up files after failure
			$this->cleanFailedTranscode($adapter, $outFilePath, $failMode);
			
			//notify listeners of failure
			$this->dispatch('onTranscodeFailure', $inFile, $preset, $outFilePath, $e);
			
			//re-throw exception so environment can handle appropriately
			throw $e;
		}
	}
	
	/**
	 * Transcode a file with a specific adapter directly.  Internally builds a dynamic preset with the specified options.
	 *
	 * @param mixed $inFile - either string filepath or instance of \AC\Mutate\File
	 * @param string $adapterName - string name of adapter to use
	 * @param string $outFile - optional output file path, if not provided will be derived automatically by the Transcoder
	 * @param string $options - key/val option hash to pass to adapter
	 * @param string $conflictMode - flag for how to handle output file conflicts
	 * @param string $failMode - flag for how to handle failed transcodes
	 * @return \AC\Mutate\File
	 */
	public function transcodeWithAdapter($inFile, $adapterName, $outFile = false, $options = array(), $conflictMode = self::ONCONFLICT_INCREMENT, $dirMode = self::ONDIR_EXCEPTION, $failMode = self::ONFAIL_DELETE) {
		//build a preset on the fly based on the options provided
		$preset = new Preset('dynamic', $adapterName, $options);
		return $this->transcodeWithPreset($inFile, $preset, $outFile, $conflictMode, $dirMode, $failMode);
	}
	
	public function transcodeWithJob($inFile, $job, $conflictMode = self::ONCONFLICT_INCREMENT, $dirMode = self::ONDIR_CREATE, $failMode = self::ONFAIL_DELETE) {
		
		if(!$job instanceof Job) {
			$job = $this->getJob($job);
		}

		//TODO: implement once the job-related APIs are defined
		throw new \RuntimeException(__METHOD__." not yet implemented.");
		
	}

	protected function processOutputFilepath($outputPath, $conflictMode, $dirMode) {
		$outputIsDirectory = $this->pathIsDirectory($outputPath);

		//check for pre-existing file and handle based on conflict mode
		if(file_exists($outputPath)) {
			if($conflictMode === self::ONCONFLICT_EXCEPTION) {
				throw new Exception\FileAlreadyExistsException("Transcode cannot run because file %s already exists.");
			}
			
			if($conflictMode === self::ONCONFLICT_DELETE) {
				if($outputIsDirectory) {
					$this->removeDirectory($outputPath);
				} else {
					@unlink($outputPath);
				}
			}
			
			if($conflictMode === self::ONCONFLICT_INCREMENT) {
				$outputPath = $this->incrementConflictingPath($outputPath);
			}
		}
		
		//check for necessary containing directory creation, handle based on directory mode
		$outputDirectory = dirname($outputPath);

		if(!file_exists($outputDirectory)) {
			if($dirMode === self::ONDIR_EXCEPTION) {
				throw new Exception\InvalidModeException("The Transcoder is not permitted to create new directories if needed.");
			}
			
			//try creating the necessary containing directories recursively
			if(!mkdir($outputDirectory, $this->getDirectoryCreationMode(), true)) {
				throw new Exception\FilePermissionException("The required containing directories could not be created.");
			}
		}

		//check for write permissions
		if(!is_writable($outputDirectory)) {
			throw new Exception\FilePermissionException(sprintf("Cannot transcode because the directory %s is not writable.", $outputDirectory));
		}
		
		//if the output is a directory, make sure the actual required directory is created
		if($outputIsDirectory) {
			if(!mkdir($outputPath, $this->getDirectoryCreationMode())) {
				throw new Exception\FilePermissionException(sprintf("Could not properly create the required output directory %s.", $outputPath));
			}
		}
		
		return $outputPath;
	}
	
	protected function incrementConflictingPath($path) {
		$isDir = $this->pathIsDirectory($path);
		$expPath = explode(DIRECTORY_SEPARATOR, $path);
		$oldFileName = array_pop($expPath);
		$basePath = implode(DIRECTORY_SEPARATOR, $expPath);
		if($isDir) {
			//for directories append incremented number after underscore
			$i = 1;
			while(file_exists($newFileName = $basePath.DIRECTORY_SEPARATOR.$oldFileName."_".$i)) {
				$i++;
			}
		} else {
			//for files insert incremented number between filename and extension
			$exp = explode(".", $oldFileName);
			$extension = array_pop($exp);
			$name = implode(".", $exp);
			$i = 1;
			while(file_exists($newFileName = $basePath.DIRECTORY_SEPARATOR.$name.".".$i.".".$extension)) {
				$i++;
			}
		}
		
		return $newFileName;
	}

	protected function removeDirectory($path) {
		foreach(scandir($path) as $item) {
			if(!in_array($item, array('.','..'))) {
				@unlink($path.DIRECTORY_SEPARATOR.$item);
			}
		}
		
		if(!rmdir($path)) {
			throw new Exception\FilePermissionException(sprintf("Could not remove directory %s", $path));
		}
	}
		
	protected function pathIsDirectory($path) {
		$exp = explode(DIRECTORY_SEPARATOR, $path);
		$name = end($exp);
		$exp = explode(".", $name);
		
		return !(count($exp) >= 2);
	}
	
	protected function cleanOutputFile(File $file) {
		if($file->isDir()) {
			chmod($file->getRealPath(), $this->getDirectoryCreationMode());
		} else {
			chmod($file->getRealPath(), $this->getFileCreationMode());
		}
	}
	
	protected function cleanFailedTranscode($adapter, $outputFilePath, $failMode) {
		if(file_exists($outputFilePath)) {
			if($failMode === self::ONFAIL_DELETE) {
				@unlink($outputFilePath);
			}
		}
		
		$adapter->cleanFailedTranscode($outputFilePath);
	}
		
	public function registerListener(TranscodeEventListener $listener) {
		$this->listeners[get_class($listener)] = $listener;
		return $this;
	}
	
	public function removeListener($className) {
		if(isset($this->listeners[$className])) {
			unset($this->listeners[$className]);
		}
		
		return $this;
	}
	
	public function hasListener($className) {
		return isset($this->listeners[$className]);
	}
	
	/**
	 * Call all listeners with the given function name and any arguments provided.  This implementation is likely to change.
	 *
	 */
	protected function dispatch() {
		try {
			$args = func_get_args();
			$methodName = array_shift($args);
		
			//call all listeners
			foreach($this->listeners as $listener) {
				call_user_func_array(array($listener, $methodName), $args);
			}
		} catch (\Exception $e) {
			//swallow exceptions thrown by listeners, they shouldn't interfere with the process, they are supposed to be passive observers
			return true;
		}
		
		return true;
	}
	
	public function getAdapter($name) {
		if(!isset($this->adapters[$name])) {
			throw new Exception\AdapterNotFoundException(sprintf("Requested adapter %s was not found in the Transcoder.", $name));
		}
		
		return $this->adapters[$name];
	}
	
	public function hasAdapter($name) {
		return isset($this->adapters[$name]);
	}
	
	public function registerAdapter(Adapter $adapter) {
		$this->adapters[$adapter->getName()] = $adapter;

		return $this;
	}
	
	public function removeAdapter($name) {
		if(isset($this->adapters[$name])) {
			unset($this->adapters[$name]);
		}

		return $this;
	}
	
	public function getAdapters() {
		return $this->adapters;
	}
	
	public function getPreset($name) {
		if(!isset($this->presets[$name])) {
			throw new Exception\PresetNotFoundException(sprintf("Requested preset %s was not found in the Transcoder.", $name));
		}

		return $this->presets[$name];
	}
	
	public function hasPreset($name) {
		return isset($this->presets[$name]);
	}
	
	public function registerPreset(Preset $preset) {
		$this->presets[$preset->getName()] = $preset;

		return $this;
	}
	
	public function removePreset($name) {
		if(isset($this->presets[$name])) {
			unset($this->presets[$name]);
		}

		return $this;
	}
	
	public function getPresets() {
		return $this->presets;
	}
	
	public function getJob($name) {
		if(!isset($this->jobs[$name])) {
			throw new Exception\JobNotFoundException(sprintf("Requested job %s was not found in the Transcoder.", $name));
		}

		return $this->jobs[$name];
	}
	
	public function hasJob($name) {
		return isset($this->jobs[$name]);
	}
	
	public function registerJob(Job $job) {
		$this->jobs[$job->getName()] = $job;

		return $this;
	}
	
	public function removeJob($name) {
		if(isset($this->jobs[$name])) {
			unset($this->jobs[$name]);
		}

		return $this;
	}
	
	public function getJobs() {
		return $this->jobs;
	}
	
	public function getFileCreationMode() {
		return $this->fileCreationMode;
	}
	
	public function setFileCreationMode($mode) {
		//force format into octal if a string was received, for example "755" instead of 0755
		if(0 != $mode[0]) {
			$mode = "0".$mode;
		}

		$this->fileCreationMode = (int) $mode;
		return $this;
	}
	
	public function getDirectoryCreationMode() {
		return $this->directoryCreationMode;
	}
	
	public function setDirectoryCreationMode($mode) {
		//force format into octal if a string was received, for example "755" instead of 0755
		if(0 != $mode[0]) {
			$mode = "0".$mode;
		}

		$this->directoryCreationMode = (int) $mode;
		return $this;
	}
	
}