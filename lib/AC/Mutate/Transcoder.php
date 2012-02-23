<?php

namespace AC\Mutate;

class Transcoder {
	
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
	 * Array of registered TranscoderEventListeners
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
	 * @return void - \AC\Mutate\File instance for newly created file
	 */
	public function transcodeWithPreset($inFile, $preset, $outFile = false, $conflictMode = self::ONCONFLICT_INCREMENT, $failMode = self::ONFAIL_DELETE) {

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
		$adapter = $this->getAdapter($preset->getAdapter());
		
		//verify if this adapter can work in the current environment (happens only the first time it's loaded)
		if(!$adapter->verify()) {
			throw new \RuntimeException($adapter->getVerificationError());
		}
		
		//have adapter verify inputs
		$adapter->validateInputFile($inFile);
		$adapter->validatePreset($preset);
		
		//generate the final output string
		$outFilePath = $preset->generateOutputPath($inFile, $outFile);
		
		//make sure the output path is valid
		$outFilePath = $this->processOutputFilepath($preset->getOutputDefinition(), $outFilePath, $conflictMode);

		try {
			//notify listeners of transcode start
			$this->dispatch('onTranscodeStart', $inFile, $preset, $outFilePath);

			//run the transcode
			$return = $adapter->transcodeFile($inFile, $outFilePath, $preset);

			//validate return
			if(!$return instanceof File) {
				throw new Exception\InvalidOutputException("Adapters must return an instance of AC\Mutate\File, or throw an exception upon error.");
			}

			$preset->validateOutputFile($return);
			$adapter->validateOutputFile($return);

		} catch (\Exception $e) {
			//clean up files after failure
			$this->cleanFailedTranscode($outFilePath, $failMode);
			
			//notify listeners of failure
			$this->dispatch('onTranscodeFailure', $inFile, $preset, $e);
			
			//re-throw exception so environment can handle appropriately
			throw $e;
		}
		
		//notify listeners of completion
		$this->dispatch('onTranscodeComplete', $return);
		
		//return new file
		return $return;
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
	public function transcodeWithAdapter($inFile, $adapterName, $outFile = false, $options = array(), $conflictMode = self::ONCONFLICT_INCREMENT, $failMode = self::ONFAIL_DELETE) {
		//build a preset on the fly based on the options provided
		$preset = new Preset('dynamic', $adapterName, $options);
		return $this->transcodeWithPreset($inFile, $preset, $outFile, $conflictMode, $failMode);
	}
	
	protected function processOutputFilepath(FileHandlerDefinition $outputDefinition, $outputPath, $conflictMode) {
		//check for write permissions
		
		//check for pre-existing file
		
		//check for directory creation
		
		//check for conflict modes, generate modified path if necessary
		
		//throw exception if invalid
		
		return $string;
	}
	
	protected function cleanFailedTranscode($outputFilePath, $failMode) {
		if(file_exists($outFilePath)) {
			if($failMode === self::ONFAIL_DELETE) {

				//TODO: check for directories

				@unlink($outFilePath);

			}
		}
	}
		
	public function transcodeWithJob($inFile, $job) {
		
		//TODO: implement
		
	}
	
	public function registerListener(EventListener $listener) {
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
	
}