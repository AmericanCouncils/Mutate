<?php

namespace AC\Mutate;

abstract class Adapter {
	protected $name = false;
	protected $description = false;
	protected $definition = false;

	public function __construct() {
		$this->definition = $this->buildDefinition();
	}

	protected function buildDefinition() {
		return new FileHandlerDefinition;
	}

	public function transcodeFile(File $file, Preset $preset, $outFilePath) {
		throw new \RuntimeException("Adapter::transcodeFile must be implemented by an extending class.");
	}
	
	public function validatePreset(Preset $preset) {
	}
	
	public function validateInputFile(File $file) {
		//scan input file, check input restrictions
	}
	
	public function verifyEnvironment() {
		return true;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getDefinition() {
		return $this->definition;
	}
}
