<?php

namespace AC\Mutate;

class File extends \SplFileInfo {
	private $_path = false;
	
	public function __construct($path) {
		$this->_path = realpath($path);
	}
	
	public function exists() {
		return file_exists($this->_path);
	}
	
	public function getExtension() {
		$exp = explode(".", $this->_path);

		return end($exp);
	}
}