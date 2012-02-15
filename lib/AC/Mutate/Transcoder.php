<?php

namespace AC\Mutate;

use \AC\Mutate\Preset\Preset;
use \AC\Mutate\Preset\PresetDefinition;

class Transcoder {
	protected $adapters = array();
	protected $presets = array();
	protected $jobs = array();

	public function transcodeWithAdapter($inFile, $adapterName, $outFile, $options = array(), $defSettings = array()) {
		//build a preset on the fly based on the options provided
		$preset = new Preset('dynamic', $adapterName, $options, new PresetDefinition($defSettings));
		return $this->transcodeWithPreset($inFile, $preset, $outFile);
	}
	
	public function transcodeWithPreset($inFile, $preset, $outFile = false, $overwrite = false) {
		//get preset
		if(!$preset instanceof Preset) {
			$preset = $this->getPreset($preset);
		}
		
		//standardize input format
		if(!$inFile instanceof File && is_string($inFile)) {
			$inFile = new File($inFile);
		}
		
		//does input actually exist?
		if(!$inFile->exists()) {
			throw new Exception\FileNotFoundException(sprintf("Input file %s could not be found.", $inFile->getFilename()));
		}
		
		//have preset validate file
		$preset->getDefinition()->validateInputFile($inFile);

		//get adapter
		$adapter = $this->getAdapter($preset->getAdapter());
		
		//have adapter validate file
		$adapter->validateInputFile($inFile);
		
		//have adapter validate preset
		$adapter->validatePreset($preset);
		
		//generate the final output string
		$outFile = $preset->generateOutputPath($outFile);
		
		//make sure the output path is usable
		$this->processOutputFilepath($preset->getDefinition(), $outFile, $overwrite);
				
		//check for incoming file extension restriction
		$restrictions = $preset->getExtensionRestrictions();
		if(!empty($restrictions) && !in_array(strtolower($inFile->getExtension()), $restrictions)) {
			throw new Exception\InvalidInputException(sprintf("Preset %s will not except files with extension %s", $preset->getName(), $inFile->getExtension()));
		}

		//run the transcode
		$return = $adapter->transcodeFile($inFile, $outFile, $preset);
		
		//enforce proper return format
		if(!$return instanceof File) {
			throw new Exception\InvalidOutputException("Adapters must return an instance of AC\Mutate\File, or throw an exception upon error.");
		}
		
		//process returned file according to preset definition
		$this->processReturnedFile($preset->getDefinition(), $return);
		
		return $return;
	}
	
	protected function processOutputFilepath(PresetDefinition $def, $path, $overwrite) {
		//check for write permissions
		$dir = is_dir($path) ? $path : dirname($path);
		if(!is_writable($dir)) {
			throw new Exception\FilePermissionException(sprintf("The output location (%s) is not writable by this process.", $path));
		}
		
		//check for pre-existing file
		if(file_exists($path) && !$overwrite) {
			throw new Exception\FileAlreadyExistsException(sprintf("The file %s already exists, you must force the overwrite option to re-run the process.", $path));
		} else if(file_exists($path)) {
			//delete pre-existing file
			unlink($path);
		}
		
		//TODO: check for directory creation
		
		return $string;
	}
	
	protected function processReturnedFile(PresetDefinition $def, File $file) {
		//TODO: set proper permissions
	}
		
	public function transcodeWithJob($inFile, $job) {
		//TODO: implement
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