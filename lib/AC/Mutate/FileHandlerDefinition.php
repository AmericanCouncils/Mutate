<?php
namespace AC\Mutate;

/**
 * FileHandlerDefinition's define valid input/output file type properties for a preset and/or adapter.  The FileHandlerDefinition is checked by the Transcoder before executing.
 */
class FileHandlerDefinition implements \Serializable {

	//directory handling
	protected $fileCreationMode = 0644;
	protected $directoryCreationMode = 0755;
	protected $allowDirectoryInput = false;
	protected $allowDirectoryOutput = false;
	protected $allowDirectoryCreation = false;

	//input restrictions
	protected $allowedInputExtensions = false;
	protected $rejectedInputExtensions = false;
	protected $allowedInputMimes = false;
	protected $rejectedInputMimes = false;
	protected $allowedInputMimeTypes = false;
	protected $rejectedInputMimeTypes = false;
	protected $allowedInputMimeEncodings = false;
	protected $rejectedInputMimeEncodings = false;

	//output restrictions
	protected $allowedOutputExtensions = false;
	protected $rejectedOutputExtensions = false;
	protected $allowedOutputMimes = false;
	protected $rejectedOutputMimes = false;
	protected $allowedOutputMimeTypes = false;
	protected $rejectedOutputMimeTypes = false;
	protected $allowedOutputMimeEncodings = false;
	protected $rejectedOutputMimeEncodings = false;

	//general i/o type
	protected $outputExtension = false;
	protected $inheritOutputExtension = false;
	protected $inputType = 'file';
	protected $outputType = 'file';
	
	/**
	 * Optionally set any properties via a hash in the constructor
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
	public function acceptsInputFile(File $file) {
		try {
			return $this->validateInputFile($file);
		} catch (\Exception $e) {
			return false;
		}
	}
	
	/**
	 * Return boolean for whether or not the FileHandlerDefinition will accept a given file.
	 *
	 * @param File $file 
	 * @return boolean
	 */
	public function acceptsOutputFile(File $file) {
		try {
			return $this->validateOutputFile($file);
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
	public function validateInputFile(File $file) {
		if(!$this->acceptsInputExtension($file->getExtension())) {
			throw new Exception\InvalidInputException(sprintf("This definition does not accept files with extension %s", $file->getExtension()));
		}
		
		if(!$this->acceptsInputMime($file->getMime())) {
			throw new Exception\InvalidInputException(sprintf("This definition does not accept files with mime of %s", $file->getMime()));
		}
		
		if(!$this->acceptsInputMimeType($file->getMimeType())) {
			throw new Exception\InvalidInputException(sprintf("This definition does not accept files with mime type of %s", $file->getMimeType()));
		}

		if(!$this->acceptsInputMimeEncoding($file->getMimeEncoding())) {
			throw new Exception\InvalidInputException(sprintf("This definition does not accept files with mime encoding of %s", $file->getMimeEncoding()));
		}
		
		if(($file->isDir() && $this->inputType !== 'directory') || ($file->isDir() && !$this->allowDirectoryInput)) {
			throw new Exception\InvalidInputException("This definition cannot accept a directory as input");
		}
		
		if(!$file->isDir() && $this->inputType === 'directory') {
			throw new Exception\InvalidInputException("This definition only accepts a directories as input");
		}

		return true;
	}
	
	/**
	 * Return true if FileHandlerDefinition accepts a given file, otherwise throw an exception on failure.
	 *
	 * @param File $file 
	 * @return boolean
	 */
	public function validateOutputFile(File $file) {
		if(!$this->acceptsOutputExtension($file->getExtension())) {
			throw new Exception\InvalidOutputException(sprintf("This definition %s should not create files with extension %s", get_class($this), $file->getExtension()));
		}
		
		if(!$this->acceptsOutputMime($file->getMime())) {
			throw new Exception\InvalidOutputException(sprintf("This definition %s should not create files with mime of %s", get_class($this), $file->getMime()));
		}
		
		if(!$this->acceptsOutputMimeType($file->getMimeType())) {
			throw new Exception\InvalidOutputException(sprintf("This definition %s should not create files with mime type of %s", get_class($this), $file->getMimeType()));
		}

		if(!$this->acceptsOutputMimeEncoding($file->getMimeEncoding())) {
			throw new Exception\InvalidOutputException(sprintf("This definition %s should not create files with mime encoding of %s", get_class($this), $file->getMimeEncoding()));
		}

		if($file->isDir() && ($this->outputType !== 'directory' || !$this->allowDirectoryOutput)) {
			throw new Exception\InvalidOutputException("This definition does not support directories as output");
		}
		
		if(!$file->isDir() && $this->outputType === 'directory') {
			throw new Exception\InvalidOutputException("This definition only supports directories as output");
		}

		return true;
	}


	public function acceptsInputExtension($ext) {
		$ext = trim(strtolower($ext), ".");
		if($this->getAllowedInputExtensions() && !in_array($ext, $this->getAllowedInputExtensions())) {
			return false;
		}
		
		if($this->getRejectedInputExtensions() && in_array($ext, $this->getRejectedInputExtensions())) {
			return false;
		}
		
		return true;
	}

	public function acceptsInputMime($ext) {
		if($this->getAllowedInputMimes() && !in_array($ext, $this->getAllowedInputMimes())) {
			return false;
		}
		
		if($this->getRejectedInputMimes() && in_array($ext, $this->getRejectedInputMimes())) {
			return false;
		}
		
		return true;
	}
	
	public function acceptsInputMimeType($ext) {
		if($this->getAllowedInputMimeTypes() && !in_array($ext, $this->getAllowedInputMimeTypes())) {
			return false;
		}
		
		if($this->getRejectedInputMimeTypes() && in_array($ext, $this->getRejectedInputMimeTypes())) {
			return false;
		}
		
		return true;
	}
	
	public function acceptsInputMimeEncoding($ext) {
		if($this->getAllowedInputMimeEncodings() && !in_array($ext, $this->getAllowedInputMimeEncodings())) {
			return false;
		}
		
		if($this->getRejectedInputMimeEncodings() && in_array($ext, $this->getRejectedInputMimeEncodings())) {
			return false;
		}
		
		return true;
	}
	
	public function acceptsOutputExtension($ext) {
		$ext = trim(strtolower($ext), ".");
		if($this->getAllowedOutputExtensions() && !in_array($ext, $this->getAllowedOutputExtensions())) {
			return false;
		}
		
		if($this->getRejectedOutputExtensions() && in_array($ext, $this->getRejectedOutputExtensions())) {
			return false;
		}
		
		return true;
	}

	public function acceptsOutputMime($ext) {
		if($this->getAllowedOutputMimes() && !in_array($ext, $this->getAllowedOutputMimes())) {
			return false;
		}
		
		if($this->getRejectedOutputMimes() && in_array($ext, $this->getRejectedOutputMimes())) {
			return false;
		}
		
		return true;
	}
	
	public function acceptsOutputMimeType($ext) {
		if($this->getAllowedOutputMimeTypes() && !in_array($ext, $this->getAllowedOutputMimeTypes())) {
			return false;
		}
		
		if($this->getRejectedOutputMimeTypes() && in_array($ext, $this->getRejectedOutputMimeTypes())) {
			return false;
		}
		
		return true;
	}
	
	public function acceptsOutputMimeEncoding($ext) {
		if($this->getAllowedOutputMimeEncodings() && !in_array($ext, $this->getAllowedOutputMimeEncodings())) {
			return false;
		}
		
		if($this->getRejectedOutputMimeEncodings() && in_array($ext, $this->getRejectedOutputMimeEncodings())) {
			return false;
		}
		
		return true;
	}

	public function getOutputExtension() {
		return $this->outputExtension;
	}
	
	public function getInheritOutputExtension() {
		return $this->inheritOutputExtension;
	}
	
	public function setInheritOutputExtension($bool) {
		$this->setInheritOutputExtension = (bool) $bool;
		return $this;
	}
	
	public function setOutputExtension($ext) {
		$this->outputExtension = $ext;
		return $this;
	}
		
	public function getAllowDirectoryInput() {
		return $this->allowDirectoryInput;
	}
	
	public function getAllowDirectoryOutput() {
		return $this->allowDirectoryOutput;
	}

	public function setAllowDirectoryInput($bool) {
		$this->allowDirectoryInput = (bool) $bool;
		return $this;
	}
	
	public function setAllowDirectoryOutput($bool) {
		$this->allowDirectoryOutput = (bool) $bool;
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
	
	public function getInputType() {
		return $this->inputType;
	}
	
	public function setInputType($type) {
		if(!in_array($type, array('file','directory'))) {
			throw new \InvalidArgumentException("Input type must be either 'file' or 'directory'.");
		}
		
		$this->inputType = $type;
		return $this;
	}
	
	public function getOutputType() {
		return $this->outputType;
	}
	
	public function setOutputType($type) {
		if(!in_array($type, array('file','directory'))) {
			throw new \InvalidArgumentException("Output type must be either 'file' or 'directory'.");
		}
		
		$this->oututType = $type;
		return $this;
	}


	/**
	 * Input restriction methods below
	 */

	public function getAllowedInputExtensions() {
		return $this->allowedInputExtensions;
	}
	
	public function setAllowedInputExtensions(array $arr) {
		$this->allowedInputExtensions = $arr;
		return $this;
	}

	public function getAllowedInputMimes() {
		return $this->allowedInputMimes;
	}
	
	public function setAllowedInputMimes(array $arr) {
		$this->allowedInputMimes = $arr;
		return $this;
	}

	public function getAllowedInputMimeTypes() {
		return $this->allowedInputMimeTypes;
	}
	
	public function setAllowedInputMimeTypes(array $arr) {
		$this->allowedInputMimeTypes = $arr;
		return $this;
	}

	public function getAllowedInputMimeEncodings() {
		return $this->allowedInputMimeEncodings;
	}
	
	public function setAllowedInputMimeEncodings(array $arr) {
		$this->allowedInputMimeEncodings = $arr;
		return $this;
	}

	public function getRejectedInputExtensions() {
		return $this->rejectedInputExtensions;
	}
	
	public function setRejectedInputExtensions(array $arr) {
		$this->rejectedInputExtensions = $arr;
		return $this;
	}

	public function getRejectedInputMimes() {
		return $this->rejectedInputMimes;
	}
	
	public function setRejectedInputMimes(array $arr) {
		$this->rejectedInputMimes = $arr;
		return $this;
	}

	public function getRejectedInputMimeTypes() {
		return $this->rejectedInputMimeTypes;
	}
	
	public function setRejectedInputMimeTypes(array $arr) {
		$this->rejectedInputMimeTypes = $arr;
		return $this;
	}

	public function getRejectedInputMimeEncodings() {
		return $this->rejectedInputMimeEncodings;
	}
	
	public function setRejectedInputMimeEncodings(array $arr) {
		$this->rejectedInputMimeEncodings = $arr;
		return $this;
	}

	/**
	 * Output restriction methods below
	 */
 	public function getAllowedOutputExtensions() {
 		return $this->allowedOutputExtensions;
 	}
	
 	public function setAllowedOutputExtensions(array $arr) {
 		$this->allowedOutputExtensions = $arr;
 		return $this;
 	}

 	public function getAllowedOutputMimes() {
 		return $this->allowedOutputMimes;
 	}
	
 	public function setAllowedOutputMimes(array $arr) {
 		$this->allowedOutputMimes = $arr;
 		return $this;
 	}

 	public function getAllowedOutputMimeTypes() {
 		return $this->allowedOutputMimeTypes;
 	}
	
 	public function setAllowedOutputMimeTypes(array $arr) {
 		$this->allowedOutputMimeTypes = $arr;
 		return $this;
 	}

 	public function getAllowedOutputMimeEncodings() {
 		return $this->allowedOutputMimeEncodings;
 	}
	
 	public function setAllowedOutputMimeEncodings(array $arr) {
 		$this->allowedOutputMimeEncodings = $arr;
 		return $this;
 	}

 	public function getRejectedOutputExtensions() {
 		return $this->rejectedOutputExtensions;
 	}
	
 	public function setRejectedOutputExtensions(array $arr) {
 		$this->rejectedOutputExtensions = $arr;
 		return $this;
 	}

 	public function getRejectedOutputMimes() {
 		return $this->rejectedOutputMimes;
 	}
	
 	public function setRejectedOutputMimes(array $arr) {
 		$this->rejectedOutputMimes = $arr;
 		return $this;
 	}

 	public function getRejectedOutputMimeTypes() {
 		return $this->rejectedOutputMimeTypes;
 	}
	
 	public function setRejectedOutputMimeTypes(array $arr) {
 		$this->rejectedOutputMimeTypes = $arr;
 		return $this;
 	}

 	public function getRejectedOutputMimeEncodings() {
 		return $this->rejectedOutputMimeEncodings;
 	}
	
 	public function setRejectedOutputMimeEncodings(array $arr) {
 		$this->rejectedOutputMimeEncodings = $arr;
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