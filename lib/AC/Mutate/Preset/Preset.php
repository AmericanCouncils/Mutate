<?php

namespace AC\Mutate\Preset;
use \AC\Mutate\File;

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
	
	public function __construct($name = false, $adapter = false, $options = array(), PresetDefinition $def = null) {
		if(!$this->name) {
			$this->name = $name;
		}
		if(!$this->adapter) {
			$this->requiredAdapter = $adapter;
		}

		$this->options = $options;

		$this->definition = ($def) ? $def : $this->buildDefinition();

		$this->configure();
	}
	
	protected function configure() {
	}
	
	protected function buildDefinition() {
		return new PresetDefinition;
	}
	
	public function validateInputFile(File $file) {
		//TODO: move logic from Transcoder to here
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
