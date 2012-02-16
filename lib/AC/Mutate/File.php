<?php

namespace AC\Mutate;

class File extends \SplFileObject {
	private $_realpath = false;
	private $_finfo_mime_type = false;
	private $_finfo_mime_encoding = false;
	private $_finfo_mime = false;
	
	public function __construct($path) {
		parent::__construct($path);
		$this->_realpath = realpath($path);
	}
	
	public function getContents() {
		return file_get_contents($this->_realpath);
	}
	
	public function getExtension() {
		return pathinfo($this->getFilename(), PATHINFO_EXTENSION);
	}
	
	public function getMimeType() {
		return $this->getFinfoMimeType()->file($this->_realpath);
	}

	public function getMimeEncoding() {
		return $this->getFinfoMimeEncoding()->file($this->_realpath);
	}
	
	public function getMime() {
		return $this->getFinfoMime()->file($this->_realpath);
	}

	private function getFinfoMime() {
		if(!$this->_finfo_mime) {
			$this->_finfo_mime = new \finfo(FILEINFO_MIME);
		}
		
		return $this->_finfo_mime;
	}

	private function getFinfoMimeType() {
		if(!$this->_finfo_mime_type) {
			$this->_finfo_mime_type = new \finfo(FILEINFO_MIME_TYPE);
		}
		
		return $this->_finfo_mime_type;
	}

	private function getFinfoMimeEncoding() {
		if(!$this->_finfo_mime_encoding) {
			$this->_finfo_mime_encoding = new \finfo(FILEINFO_MIME_ENCODING);
		}
		
		return $this->_finfo_mime_encoding;
	}
}