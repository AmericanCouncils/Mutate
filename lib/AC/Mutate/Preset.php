<?php

namespace AC\Mutate;

class Preset implements \ArrayAccess, \Serializable {
	/**
	 * A human-readable string name for the preset.
	 *
	 * @var string
	 */
	protected $name = false;
	
	/**
	 * A string name of the required adapter for this preset.  The string should match the string returned from Adapter::getname() in the adapter required by this preset
	 *
	 * @var string
	 */
	protected $requiredAdapter = false;

	/**
	 * A human-readable description for what the preset does.
	 *
	 * @var string
	 */
	protected $description = "No description provided.";
	
	/**
	 * FileHandlerDefinition instance built during __construct(); that describes input file restrictions.
	 *
	 * @var \AC\Mutate\FileHandlerDefinition
	 */
	protected $inputDefinition = false;
	
	/**
	 * FileHandlerDefinition instance built during __construct(); that describes output file restrictions.
	 *
	 * @var \AC\Mutate\FileHandlerDefinition
	 */
	protected $outputDefinition = false;
	
	/**
	 * Boolean for whether or not the preset is locked.  If locked, options cannot be modified, only read
	 *
	 * @var boolean
	 */
	protected $locked = false;	
	
	/**
	 * Options are values specific to the adapter required by the prefix.
	 *
	 * @var array
	 */
	protected $options = array();
	
	/**
	 * Constructor - all values are optional so that Presets can be defind on-the-fly, or by extension.
	 *
	 * @param string $name 
	 * @param string $adapter 
	 * @param array $options 
	 */
	public function __construct($name = false, $requiredAdapter = false, $options = array()) {
		//if already set (by extension), don't override
		if(!$this->name) {
			$this->name = $name;
		}

		//if already set (by extension), don't override
		if(!$this->requiredAdapter) {
			$this->requiredAdapter = $requiredAdapter;
		}

		$this->options = $options;

		$this->inputDefinition = $this->buildInputDefinition();
		$this->outputDefinition = $this->buildOutputDefinition();

		$this->configure();
		
		//make sure we have the requirements
		if(!$this->name) {
			throw new Exception\InvalidPresetException("Presets require a valid name to be specified.");
		}
		
		if(!$this->requiredAdapter) {
			throw new Exception\InvalidPresetException("Presets must declared their required adapter.");
		}
		
		if(!$this->inputDefinition) {
			throw new Exception\InvalidPresetException("Missing input FileDefinitionHandler, did you forget to return it from Preset::buildInputDefinition() ?");
		}

		if(!$this->outputDefinition) {
			throw new Exception\InvalidPresetException("Missing output FileDefinitionHandler, did you forget to return it from Preset::buildOutputDefinition() ?");
		}
	}
	
	/**
	 * Return string name of this preset.
	 *
	 * @return string
	 * @author Evan Villemez
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Return string name of required adapter
	 *
	 * @return string
	 */
	public function getRequiredAdapter() {
		return $this->requiredAdapter;
	}
	
	/**
	 * Return string human-readable description of what this preset does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * For overriding classes to extend.  Generally specific preset options should be defined in this method when extending.
	 *
	 * @return void
	 */
	protected function configure() {}
	
	/**
	 * Meant to be overriden in extending preset classes.  The default FileHandlerDefinition will accept files of any format.
	 *
	 * @return FileHandlerDefinition
	 */
	protected function buildInputDefinition() {
		return new FileHandlerDefinition;
	}
	
	/**
	 * Meant to be overriden in extending preset classes.  The default FileHandlerDefinition will accept files of any format.
	 *
	 * @return FileHandlerDefinition
	 */
	protected function buildOutputDefinition() {
		return new FileHandlerDefinition;
	}

	/**
	 * Uses input FileHandlerDefinition to validate given file. Throws exceptions on failure.
	 *
	 * @param File $file 
	 * @return true
	 */
	public function validateInputFile(File $file) {
		return $this->getInputDefinition()->validateFile($file);
	}

	/**
	 * Uses output FileHandlerDefinition to validate given file. Throws exceptions on failure.
	 *
	 * @param File $file 
	 * @return true
	 */
	public function validateOutputFile(File $file) {
		return $this->getOutputDefinition()->validateFile($file);
	}

	/**
	 * Uses input FileHandlerDefinition to accepts given file. Throws exceptions on failure.
	 *
	 * @param File $file 
	 * @return true
	 */
	public function acceptsInputFile(File $file) {
		return $this->getInputDefinition()->acceptsFile($file);
	}

	/**
	 * Uses output FileHandlerDefinition to accepts given file. Throws exceptions on failure.
	 *
	 * @param File $file 
	 * @return true
	 */
	public function acceptsOutputFile(File $file) {
		return $this->getOutputDefinition()->acceptsFile($file);
	}
	
	/**
	 * Returns suggest string output path, given a user provided path and input file.
	 *
	 * @param File $inFile 
	 * @param string $outputPath, or default to false
	 * @return string
	 */
	public function generateOutputPath(File $inFile, $outputPath = false) {
		//if we have a path
		if(is_string($outputPath)) {
			//if that path is a directory
			if(is_dir($outputPath)) {
				
			} else {
				//otherwise
			
			}
		}
		
		//if we don't have an output path...
		if(!$outputPath) {
			//check definition required types
			//inherit path from input file
		}
		//else if has extension:
			//
		
		//TODO: implement, use $this->resolveOutputExtension as needed
		return $outputFilePath;
	}
	
	/**
	 * Resolves the output extension based on the input file.  If it cannot be determined, will call Preset::getOutputExtension, which must
	 * be implemented by an extending class, otherwise exceptions are thrown.
	 *
	 * @param File $inFile 
	 * @return string
	 */
	protected function resolveOutputExtension(File $inFile) {
		if(!$inFile->isDir()) {
			//TODO: implement, use $this->getOutputExtension() as necessary			
		}
	}
	
	/**
	 * Method for determining the output extension based on data from the preset.  This must be implemented
	 * by an extending class, and is only called if the output extension can't be determined any other way.
	 *
	 * @return string
	 */
	protected function getOutputExtension() {
		throw new Exception\InvalidPresetException(__METHOD__." must be implemented by an extending class to properly determine the required output extension.");
	}
		
	/**
	 * Return input FileHandlerDefinition
	 *
	 * @return AC\Mutate\FileHandlerDefinition
	 */
	public function getInputDefinition() {
		return $this->inputDefinition;
	}

	/**
	 * Return output FileHandlerDefinition
	 *
	 * @return AC\Mutate\FileHandlerDefinition
	 */
	public function getOutputDefinition() {
		return $this->outputDefinition;
	}
	
	/**
	 * Set input FileHandlerDefinition (if not locked)
	 *
	 * @param FileHandlerDefinition $def 
	 * @return self
	 */
	public function setInputDefinition(FileHandlerDefinition $def) {
		if(!$this->locked) {
			$this->inputDefinition = $def;
		}

		return $this;
	}
	
	/**
	 * Set output FileHandlerDefinition (if not locked)
	 *
	 * @param FileHandlerDefinition $def 
	 * @return self
	 */
	public function setOutputDefinition(FileHandlerDefinition $def) {
		if(!$this->locked) {
			$this->outputDefinition = $def;
		}

		return $this;
	}
	
	/**
	 * Set array of preset options in one operation.
	 *
	 * @param array $ops
	 * @return self
	 */
	public function setOptions(array $ops) {
		$this->options = $ops;
		return $this;
	}
	
	/**
	 * Sets 'locked' property to true, so that no new options can be set or removed.
	 */
	public function lock() {
		$this->locked = true;
	}

	/**
	 * Retrieve one option by key, returning a default value if not set
	 *
	 * @param string $key 
	 * @param mixed $default 
	 * @return mixed
	 */
	public function get($key, $default = null) {
		return isset($this->options[$key]) ? $this->options[$key] : $default;
	}
	
	/**
	 * Set a key / value option pair (if not locked)
	 *
	 * @param string $key 
	 * @param mixed $val 
	 * @return self
	 */
	public function set($key, $val) {
		if(!$this->locked) {
			$this->options[$key] = $val;
		}

		return $this;
	}
	
	/**
	 * Return true/false if option key exists
	 *
	 * @param string $key 
	 * @return boolean
	 */
	public function has($key) {
		return isset($this->options[$key]);
	}
	
	/**
	 * Remove an option by key (if not locked)
	 *
	 * @param string $key 
	 * @return self
	 */
	public function remove($key) {
		if(!$this->locked) {
			if(isset($this->options[$key])) {
				unset($this->options[$key]);
			}
		}

		return $this;
	}
	
	/**
	 * ArrayAccess implementation for Preset::get()
	 */
	public function offsetGet($key) {
		return $this->get($key);
	}
	
	/**
	 * ArrayAccess implementation for Preset::set()
	 */
	public function offsetSet($key, $val) {
		return $this->set($key, $val);
	}
	
	/**
	 * ArrayAccess implementation for Preset::has()
	 */
	public function offsetExists($key) {
		return $this->has($key);
	}
	
	/**
	 * ArrayAccess implementation for Preset::remove()
	 */
	public function offsetUnset($key) {
		return $this->remove($key);
	}
	
	/**
	 * Serializable implementation
	 */
	public function serialize() {
		$data = array();
		foreach($this as $key => $val) {
			$data[$key] = $val;
		}

		return serialize($data);
	}
	
	/**
	 * Serializable implementation
	 */
	public function unserialize($string) {
		$data = unserialize($string);
		foreach($data as $key => $val) {
			$this->$key = $val;
		}
	}	
}
