<?php

namespace AC\Mutate;

abstract class Adapter {
	protected $key = false;
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
	
	/**
	 * Gets called by the Transcoder when a transcode process fails.
	 *
	 * @param string $outFilePath 
	 */
	public function cleanFailedTranscode($outFilePath) {
		return;
	}
	
	public function validatePreset(Preset $preset) {
		return true;
	}
	
	public function validateInputFile(File $file) {
		$this->getInputDefinition()->validateFile($file);
		return true;
	}
	
	public function validateOutputFile(File $file) {
		$this->getOutputDefinition()->validateFile($file);

		return true;
	}
	
	public function acceptsInputFile(File $file) {
		return $this->getInputDefinition()->acceptsFile($file);
	}

	public function acceptsOutputFile(File $file) {
		return $this->getOutputDefinition()->acceptsFile($file);
	}

	public function verify() {
		if(is_null($this->verified)) {
			try {
				$this->verified = (bool) $this->verifyEnvironment();
				if(!$this->verified) {
					throw new Exception\EnvironmentException("The adapter could not properly validate its environment.");
				}
			} catch (\Exception $e) {
				$this->verificationError = $e->getMessage();
				$this->verified = false;
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
	
	
	/**
	 * Return the key for this adapter
	 *
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}
	
	/**
	 * Return string name of this adapter, the key will be returned if a name is not defined.
	 *
	 * @return string
	 */
	public function getName() {
		if(!$this->name) {
			return $this->key;
		}
		
		return $this->name;
	}
	
	public function getDescription() {
		return $this->description;
	}
}
