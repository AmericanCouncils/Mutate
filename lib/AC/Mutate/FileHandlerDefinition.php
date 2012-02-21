<?php
namespace AC\Mutate;

/**
 * FileHandlerDefinition's define valid input/output file type properties for a preset and/or adapter.  The FileHandlerDefinition is checked by the Transcoder before executing.
 */
class FileHandlerDefinition implements \Serializable {

	//directory handling
	protected $fileCreationMode = 0644;
	protected $directoryCreationMode = 0755;
	protected $allowDirectory = false;
	protected $allowDirectoryCreation = false;

	//input restrictions
	protected $allowedExtensions = false;
	protected $rejectedExtensions = false;
	protected $allowedMimes = false;
	protected $rejectedMimes = false;
	protected $allowedMimeTypes = false;
	protected $rejectedMimeTypes = false;
	protected $allowedMimeEncodings = false;
	protected $rejectedMimeEncodings = false;

	//general i/o type
	protected $requiredExtension = false;
	protected $inheritExtension = true;
	protected $requiredFileType = 'file';
	
	/**
	 * Optionally set any properties via a hash in the constructor instead of using setter methods
	 *
	 * @param string $ops 
	 */
	public function __construct($ops = array()) {
		$this->setOptions($ops);
	}
	
	/**
	 * Return boolean for whether or not the FileHandlerDefinition will accept a given file.
	 *
	 * @param File $file 
	 * @return boolean
	 */
	public function acceptsFile(File $file) {
		try {
			return $this->validateFile($file);
		} catch (\Exception $e) {
			return false;
		}
	}
	
	/**
	 * Return true if FileHandlerDefinition accepts a given file, otherwise throw an exception on failure.
	 *
	 * @param File $file 
	 * @return boolean
	 */
	public function validateFile(File $file) {
		if(!$this->acceptsExtension($file->getExtension())) {
			throw new Exception\InvalidFileException(sprintf("This definition does not accept files with extension %s", $file->getExtension()));
		}
		
		if(!$this->acceptsMime($file->getMime())) {
			throw new Exception\InvalidFileException(sprintf("This definition does not accept files with mime of %s", $file->getMime()));
		}
		
		if(!$this->acceptsMimeType($file->getMimeType())) {
			throw new Exception\InvalidFileException(sprintf("This definition does not accept files with mime type of %s", $file->getMimeType()));
		}

		if(!$this->acceptsMimeEncoding($file->getMimeEncoding())) {
			throw new Exception\InvalidFileException(sprintf("This definition does not accept files with mime encoding of %s", $file->getMimeEncoding()));
		}
		
		if((!$this->allowDirectory && $file->isDir()) || ($this->requiredFileType === 'directory' && !$file->isDir())) {
			throw new Exception\InvalidFileException("This definition cannot accept a directory as input");
		}
		
		if(!$file->isDir() && $this->requiredFileType === 'directory') {
			throw new Exception\InvalidFileException("This definition only accepts a directories as input");
		}

		return true;
	}
	

	public function acceptsExtension($ext) {
		$ext = trim(strtolower($ext), ".");
		if($this->getAllowedExtensions() && !in_array($ext, $this->getAllowedExtensions())) {
			return false;
		}
		
		if($this->getRejectedExtensions() && in_array($ext, $this->getRejectedExtensions())) {
			return false;
		}
		
		return true;
	}

	public function acceptsMime($ext) {
		if($this->getAllowedMimes() && !in_array($ext, $this->getAllowedMimes())) {
			return false;
		}
		
		if($this->getRejectedMimes() && in_array($ext, $this->getRejectedMimes())) {
			return false;
		}
		
		return true;
	}
	
	public function acceptsMimeType($ext) {
		if($this->getAllowedMimeTypes() && !in_array($ext, $this->getAllowedMimeTypes())) {
			return false;
		}
		
		if($this->getRejectedMimeTypes() && in_array($ext, $this->getRejectedMimeTypes())) {
			return false;
		}
		
		return true;
	}
	
	public function acceptsMimeEncoding($ext) {
		if($this->getAllowedMimeEncodings() && !in_array($ext, $this->getAllowedMimeEncodings())) {
			return false;
		}
		
		if($this->getRejectedMimeEncodings() && in_array($ext, $this->getRejectedMimeEncodings())) {
			return false;
		}
		
		return true;
	}
	

	public function getInheritExtension() {
		return $this->inheritExtension;
	}
	
	public function setInheritExtension($bool) {
		$this->inheritExtension = (bool) $bool;
		return $this;
	}

	public function getAllowDirectory() {
		return $this->allowDirectory;
	}
	
	public function setAllowDirectory($bool) {
		$this->allowDirectory = (bool) $bool;
		return $this;
	}
	
	public function getAllowDirectoryCreation() {
		return $this->allowDirectoryCreation;
	}

	public function setAllowDirectoryCreation($bool) {
		$this->allowDirectoryCreation = (bool) $bool;
		return $this;
	}

	public function getDirectoryCreationMode() {
		return $this->directoryCreationMode;
	}

	public function setDirectoryCreationMode($num) {
		$this->directoryCreationMode = $num;
		return $this;
	}

	public function getFileCreationMode() {
		return $this->fileCreationMode;
	}
		
	public function setFileCreationMode($num) {
		$this->fileCreationMode = $num;
		return $this;
	}
	
	public function getRequiredFileType() {
		return $this->requiredFileType;
	}
	
	public function setRequiredFileType($type) {
		if(!in_array($type, array('file','directory'))) {
			throw new \InvalidArgumentException("Input type must be either 'file' or 'directory'.");
		}
		
		if($type === 'directory') {
			$this->setAllowDirectory(true);
		} else {
			$this->setAllowDirectory(false);
		}

		$this->requiredFileType = $type;
		return $this;
	}
		
	/**
	 *  type restriction methods below
	 */

	public function getAllowedExtensions() {
		return $this->allowedExtensions;
	}
	
	public function setAllowedExtensions(array $arr) {
		$this->allowedExtensions = $arr;
		return $this;
	}

	public function getAllowedMimes() {
		return $this->allowedMimes;
	}
	
	public function setAllowedMimes(array $arr) {
		$this->allowedMimes = $arr;
		return $this;
	}

	public function getAllowedMimeTypes() {
		return $this->allowedMimeTypes;
	}
	
	public function setAllowedMimeTypes(array $arr) {
		$this->allowedMimeTypes = $arr;
		return $this;
	}

	public function getAllowedMimeEncodings() {
		return $this->allowedMimeEncodings;
	}
	
	public function setAllowedMimeEncodings(array $arr) {
		$this->allowedMimeEncodings = $arr;
		return $this;
	}

	public function getRejectedExtensions() {
		return $this->rejectedExtensions;
	}
	
	public function setRejectedExtensions(array $arr) {
		$this->rejectedExtensions = $arr;
		return $this;
	}

	public function getRejectedMimes() {
		return $this->rejectedMimes;
	}
	
	public function setRejectedMimes(array $arr) {
		$this->rejectedMimes = $arr;
		return $this;
	}

	public function getRejectedMimeTypes() {
		return $this->rejectedMimeTypes;
	}
	
	public function setRejectedMimeTypes(array $arr) {
		$this->rejectedMimeTypes = $arr;
		return $this;
	}

	public function getRejectedMimeEncodings() {
		return $this->rejectedMimeEncodings;
	}
	
	public function setRejectedMimeEncodings(array $arr) {
		$this->rejectedMimeEncodings = $arr;
		return $this;
	}

	/**
	 * Set multiple properties as a key/val hash in one operation.
	 *
	 * @param array $ops 
	 * @return self
	 */
	public function setOptions(array $ops) {
		foreach($ops as $key => $val) {
			$this->$key = $val;
		}
		
		return $this;
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
		$this->setOptions($data);
	}
}