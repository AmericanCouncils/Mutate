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
	}
	
	/**
	 * For overriding classes to extend.  Generally specific preset options should be defined in this method when extending.
	 *
	 * @return void
	 */
	protected function configure() {
	}
	
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
	 * Returns suggest string output path, given a user provided path and input file.
	 *
	 * @param File $inFile 
	 * @param string $outputPath 
	 * @return string
	 */
	public function assembleOutputPath(File $inFile, $outputPath = false) {
		//TODO: implement
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
	protected function setOptions(array $ops) {
		$this->options = $ops;
		return $this;
	}
	
	/**
	 * Sets 'locked' property to true, so that no new options can be set or removed.
	 */
	protected function lock() {
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
