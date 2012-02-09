<?php
namespace AC\Mutate\Preset;

class PresetDefinition implements \Serializable {
	protected $requiredAdapter = false;
	protected $fileCreationMode = 0644;
	protected $directoryCreationMode = 0755;
	protected $allowDirectoryInput = false;
	protected $allowDirectoryOutput = false;
	protected $allowDirectoryCreation = false;
	protected $outputExtension = false;
	protected $allowedInputExtensions = false;
	protected $rejectedInputExtensions = false;
	protected $returnType = 'file';
	
	public function getRequiredAdapter() {
		return $this->requiredAdapter;
	}
	
	public function getAllowDirectoryInput() {
	}
	
	public function getAllowDirectoryOutput() {
	}

	public function getAllowDirectoryCreation() {
	}

	public function getDirectoryCreationMode() {
		return $this->directoryCreationMode;
	}

	public function getFileCreationMode() {
		return $this->fileCreationMode;
	}
		
	public function getInputExtensionRestrictions() {
		
	}
	
	public function getOutputExtension() {
		
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
		foreach($data as $key => $val) {
			$this->$key = $val;
		}
	}
}