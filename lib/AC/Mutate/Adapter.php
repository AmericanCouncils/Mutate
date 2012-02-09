<?php

namespace AC\Mutate;

abstract class Adapter {
	protected $name = false;
	protected $description = false;
	protected $defaultSettings = array();
	protected $inputExtensionRestrictions = false;
	protected $allowDirectoryInput = false;
	protected $allowDirectoryOutput = false;
	protected $outputFormatRestrictions = false;

	public function transcodeFile(File $file, Preset $preset, $outFilePath) {
		throw new \RuntimeException("Adapter::transcodeFile must be implemented by an extending class.");
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDescription() {
		return $this->description;
	}
}
