<?php
namespace AC\Mutate\Preset;

/**
 * PresetDefinition's define valid input/output for a preset.  The PresetDefinition is checked by the Transconder before executing.
 *
 */
class PresetDefinition implements \Serializable {
	protected $fileCreationMode = 0644;
	protected $directoryCreationMode = 0755;
	protected $inheritOutputExtension = true;
	protected $allowDirectoryInput = false;
	protected $allowDirectoryOutput = false;
	protected $allowDirectoryCreation = false;
	protected $outputExtension = false;
	protected $allowedInputExtensions = false;
	protected $rejectedInputExtensions = false;
	protected $returnType = 'file';
	
	public function __construct($ops = array()) {
		$this->setOptions($ops);
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
			$this->$key => $val;
		}
		
		return $this;
	}
	
	public function serialize() {
		$data = array() {
			foreach($this as $key => $val) {
				$data[$key] = $val;
			}
		}
		
		return serialize($data);
	}
	
	public function unserialize($string) {
		$data = unserialize($string);
		$this->setOptions($data);
	}
}