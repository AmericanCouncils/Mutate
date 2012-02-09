<?php

namespace AC\Mutate;

class Transcoder {
	protected $adapters = array();
	protected $presets = array();
	protected $jobs = array();

	public function transcodeWithAdapter($inFile, $adapterName, $outFile, $options = array()) {
		//build a preset on the fly based on the options provided
		$preset = new Preset('dynamic', $adapterName, $options);
		return $this->transcodeWithPreset($inFile, $preset, $outFile);
	}
	
	public function transcodeWithPreset($inFile, $preset, $outFile = false) {
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

		//check for directory input
		if($inFile->isDir() && !$preset->allowsDirectoryInput()) {
			throw new Exception\InvalidInputException(sprintf("Preset %s does not allow directory input, you must specify a file.", $preset->getName()));
		}
		
		//figure out output file path
		if(!$outFile) {
			//check if the preset allows directory output
			if($preset->hasDirectoryOutput()) {
				
			} else {
				
			}
		}
		
		//check output path
		$outPath = is_dir($outFile) ? $outFile : dirname($outFile);
		if(!is_writable($path)) {
			throw new Exception\FilePermissionException(sprintf("The output location (%s) is not writable by this process.", $path));
		}
		
		//get the adapter specified by the preset
		$adapter = $this->getAdapter($preset->getRequiredAdapter());

		//run the transcode
		$return = $adapter->transcodeFile($inFile, $preset, $outFile);
		
		//enforce proper return format
		if(!$return instanceof File) {
			throw new Exception\InvalidOutputException("Adapters must return an instance of AC\Mutate\File, or throw an exception upon error.");
		}
		
		return $return;
	}
		
	public function transcodeWithJob($inFile, $job, $outFile = false) {
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
			return unset($this->jobs[$name]);
		}

		return $this;
	}
	
	public function getJobs() {
		return $this->jobs;
	}
	
}