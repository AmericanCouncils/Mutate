<?php

namespace AC\Mutate;

abstract class Adapter {
	protected $name = false;
	protected $description = false;

	public function transcodeFile(File $file, $outFilePath, Preset $preset = null) {
		throw new \RuntimeException("Adapter::transcodeFile must be implemented by an extending class.");
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDescription() {
		return $this->description;
	}
}
