<?php

namespace AC\Mutate;

class Transcoder {
	protected $adapters = array();
	protected $presets = array();
	protected $jobs = array();

	public function transcodeWithAdapter($inFile, $adapterName, $outFile, $options = array(), $settings = array()) {
		//build a preset on the fly based on the options provided
		$preset = new Preset('dynamic', $adapterName, $options, $settings);
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

		//validate preset definition against input
		$def = $preset->getDefinition();
		$this->validatePresetDefinitionWithInput($inFile, $def);

		//validate adapter against preset
		$adapter = $this->getAdapter($def->getRequiredAdapter());
		$this->validateAdapterWithInputAndDefinition($inFile, $def, $adapter);
			
		//resolve output format
		$outFile = $this->resolveOutputFormat($def, $adapter, $outFile);
		if(!is_string($outFile)) {
			throw new Exception\InvalidOutputException(sprintf("Transcoder must be able to determine a string output format to pass to adapter."));
		}
		
		//validate adapter against intended output
		$this->validateAdapterWithOutput($adapter, new File($outFile));
		
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
		
		return $return;
	}
	
	protected function resolveOutputFormat(PresetDefinition $def, Adapter $adapter, $outFile = false) {
		//check output path
		$outPath = is_dir($outFile) ? $outFile : dirname($outFile);
		if(!is_writable($path)) {
			throw new Exception\FilePermissionException(sprintf("The output location (%s) is not writable by this process.", $path));
		}
		
		return $string;
	}
	
	protected function validateAdapterWithInputAndDefinition(File $file, PresetDefinition $def, Adapter $adapter) {
	
	}
	
	protected function validateAdapterWithOutput(Adapter $adapter, File $outFile) {
		//mostly directory checking, check write permissions
	}
	
	protected function validatePresetDefinitionWithInput(File $file, PresetDefinition $def) {
		if($inFile->isDir() && !$def->allowsDirectoryInput()) {
			throw new Exception\InvalidInputException(sprintf("Preset %s does not allow directory input, you must specify a file.", $preset->getName()));
		}
		
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
			unset($this->jobs[$name]);
		}

		return $this;
	}
	
	public function getJobs() {
		return $this->jobs;
	}
	
}