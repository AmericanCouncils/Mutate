<?php
namespace AC\Mutate;

/**
 * FileHandlerDefinition's define valid input/output for a preset.  The FileHandlerDefinition is checked by the Transconder before executing.
 *
 */
class FileHandlerDefinition implements \Serializable {
	protected $fileCreationMode = 0644;
	protected $directoryCreationMode = 0755;
	protected $inheritOutputExtension = true;
	protected $allowDirectoryInput = false;
	protected $allowDirectoryOutput = false;
	protected $allowDirectoryCreation = false;
	protected $outputExtension = false;
	protected $allowedInputExtensions = false;
	protected $rejectedInputExtensions = false;
	protected $allowedInputMimes = false;
	protected $rejectedInputMimes = false;
	protected $allowedInputMimeTypes = false;
	protected $rejectedInputMimeTypes = false;
	protected $allowedInputMimeEncodings = false;
	protected $rejectedInputMimeEncodings = false;
	protected $allowedOutputExtensions = false;
	protected $rejectedOutputExtensions = false;
	protected $inputType = 'file';
	protected $outputType = 'file';
	
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
	 * Return true if FileHandlerDefinition accepts a given file, otherwise throw an exception on failure.
	 *
	 * @param File $file 
	 * @return boolean
	 */
	public function validateInputFile(File $file) {
		//do checks here, throw exceptions on failure
		
		
		return true;
	}
	
	public function validateOutputFile(File $file) {
	}
	
		
	public function getAllowDirectoryInput() {
		return $this->allowDirectoryInput;
	}
	
	public function getAllowDirectoryOutput() {
		return $this->allowDirectoryOutput;
	}

	public function getAllowDirectoryCreation() {
		return $this->allowDirectoryCreation;
	}

	public function getDirectoryCreationMode() {
		return $this->directoryCreationMode;
	}

	public function getFileCreationMode() {
		return $this->fileCreationMode;
	}
		
	public function getInputExtensionRestrictions() {
		return $this->inputExtensionRestrictions;
	}
	
	public function getOutputExtensionRestrictions() {
		return $this->outputExtensionRestrictions;
	}
	
	protected function setOptions(array $ops) {
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