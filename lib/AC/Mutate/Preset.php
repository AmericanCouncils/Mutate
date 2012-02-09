<?php

namespace AC\Mutate;

class Preset implements \ArrayAccess, \Serializable, \IteratorAggregate {
	protected $name = false;
	protected $description = false;
	protected $requiredAdapter = false;
	protected $locked = false;
	protected $settings = array();
	protected $options = array();
	
	public function __construct($name, $adapter, $settings = array(), $options = array()) {
		$this->name = $name;
		$this->requiredAdapter = $adapter;
		$this->options = $options;
	}
	
	public function getRequiredAdapter() {
		return $this->requiredAdapter;
	}
	
	public function allowsDirectories() {
		return (isset($this->settings['allowDirectories']) && true === $this->settings['allowDirectories']);
	}
	
	public function getAllowedExtensions() {
		
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
