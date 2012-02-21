<?php

namespace AC\Mutate;

abstract class Adapter {
	protected $name = false;
	protected $description = false;
	protected $inputDefinition = false;
	protected $outputDefinition = false;

	private $verified = null;
	private $verificationError = false;

	protected function buildInputDefinition() {
		return new FileHandlerDefinition;
	}

	protected function buildOutputDefinition() {
		return new FileHandlerDefinition;
	}

	public function getInputDefinition() {
		if(!$this->inputDefinition) {
			$this->inputDefinition = $this->buildInputDefinition();
		}
		
		return $this->inputDefinition;
	}
	
	public function getOutputDefinition() {
		if(!$this->outputDefinition) {
			$this->outputDefinition = $this->buildOutputDefinition();
		}
		
		return $this->outputDefinition;
	}

	public function transcodeFile(File $file, Preset $preset, $outFilePath) {
		throw new \RuntimeException("Adapter::transcodeFile must be implemented by an extending class.");
	}
	
	public function validatePreset(Preset $preset) {
		return true;
	}
	
	public function validateInputFile(File $file) {
		$this->getInputDefinition()->validateFile($file);
		
		//anything else?
		
		return true;
	}
	
	public function validateOutputFile(File $file) {
		$this->getOutputDefinition()->validateFile($file);
		
		//anything else?
		
		return true;
	}

	public function verify() {
		if(is_null($this->verified)) {
			try {
				$this->verified = (bool) $this->verifyEnvironment();
			} catch (\Exception $e) {
				$this->verificationError = $e->getMessage();
				return false;
			}
		}
		
		return $this->verified;
	}
	
	public function getVerificationError() {
		return $this->verificationError;
	}
	
	/**
	 * For extending classes to implement their environment validation logic.  Should throw exceptions on failure, or return true on success.
	 *
	 * @return boolean
	 */
	protected function verifyEnvironment() {
		return true;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDescription() {
		return $this->description;
	}
}
