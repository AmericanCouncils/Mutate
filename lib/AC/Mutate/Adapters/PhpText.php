<?php

namespace AC\Mutate\Adapters;
use AC\Mutate\Preset\Preset;
use AC\Mutate\Adapter;
use AC\Mutate\File;

class PhpText extends Adapter {
	protected $name = 'php_text';
	protected $description = "Uses common php functions to manipulate the contents of a file.";
	protected $allowedFunctions = array(
		'strtolower',
		'strtoupper',
		'ucwords'
	);
	
	public function validatePreset(Preset $preset) {
		if(!$preset->get('func', false)) {
			throw new \InvalidArgumentException('Func is a required preset option.');
		}
		
		if(!in_array(strtolower($preset['func']), $this->allowedFunctions)) {
			throw new \InvalidArgumentException(sprintf("Specified function can only be one of the following: %s", implode(", ", $this->allowedFunctions)));
		}
	}
	
	public function transcodeFile(File $file, Preset $preset, $outFilePath) {
		
		file_put_contents($outFilePath, $preset['func'](file_get_contents($file->getFilename())));
		
		return new File($outFilePath);
	}

}