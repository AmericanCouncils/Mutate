<?php

namespace AC\Mutate;

class Preset implements \ArrayAccess, \Serializable {
	protected $name = false;
	protected $description = false;
	protected $adapter = false;
	protected $locked = false;
	
	
	/**
	 * Options are values specific to the adapter required by the prefix.
	 *
	 * @var array
	 */
	protected $options = array();
	
	public function __construct($name = false, $adapter = false, $options = array(), $definitionOptions = array()) {
		if(!$this->name) {
			$this->name = $name;
		}
		if(!$this->adapter) {
			$this->requiredAdapter = $adapter;
		}

		$this->options = $options;

		$this->definition = (!empty($definitionOptions)) ? new FileHandlerDefinition($definitionOptions) : $this->buildDefinition();

		$this->configure();
	}
	
	protected function configure() {
	}
	
	/**
	 * Meant to be override in extending preset classes.  The default FileHandlerDefinition will accept files of any format.
	 *
	 * @return void
	 * @author Evan Villemez
	 */
	protected function buildDefinition() {
		return new FileHandlerDefinition;
	}
	
	public function validateInputFile(File $file) {
		if(!$file->isDir() && !$this->definition->acceptsExtension($file->getExtension())) {
			throw new InvalidInputFileException(sprintf("File type (%s) format not supported by this preset.", $file->getExtension()));
		}
		
		
	}
	
	public function assembleOutputPath(File $inFile, $outputPath = false) {
		//TODO: write
	}
	
	public function getAdapter() {
		return $this->adapter;
	}
	
	public function getDefinition() {
		return $this->definition;
	}
	
	protected function setOptions(array $ops) {
		$this->options = $ops;
	}
	
	public function get($key, $default = null) {
		return isset($this->options[$key]) ? $this->options[$key] : $default;
	}
	
	public function set($key, $val) {
		if(!$this->locked) {
			$this->options[$key] = $val;
		}
		return $this;
	}
	
	public function has($key) {
		return isset($this->options[$key]);
	}
	
	public function remove($key) {
		if(!$this->locked) {
			if(isset($this->options[$key])) {
				unset($this->options[$key]);
			}
		}

		return $this;
	}
	
	public function offsetGet($key) {
		return $this->get($key);
	}
	
	public function offsetSet($key, $val) {
		return $this->set($key, $val);
	}
	
	public function offsetExists($key) {
		return $this->has($key);
	}
	
	public function offsetUnset($key) {
		return $this->remove($key);
	}
	
	public function serialize() {
		$data = array();
		foreach($this as $key => $val) {
			$data[$key] = $val;
		}
		return serialize($data);
	}
	
	public function unserialize($string) {
		$data = unserialize($string);
		foreach($data as $key => $val) {
			$this->$key = $val;
		}
	}
	
	protected function lock() {
		$this->locked = true;
	}
}
