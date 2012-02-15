<?php

namespace AC\Mutate\Adapters;
use AC\Mutate\Preset\Preset;
use AC\Mutate\Adapter;
use AC\Mutate\File;

/**
 * A very simple adapter to illustrate how writing an adapter should work.
 */
class PhpText extends Adapter {
	protected $name = 'php_text';
	protected $description = "Uses common php functions to manipulate the contents of a file.";

	/**
	 * Array of allowed methods to call on any input.
	 *
	 * @var string
	 */
	protected $allowedFunctions = array(
		'strtolower',
		'strtoupper',
		'ucwords'
	);
	
	/**
	 * Make sure 'func' parameter has been set and is an allowed value.
	 */
	public function validatePreset(Preset $preset) {
		if(!$preset->get('func', false)) {
			throw new \InvalidArgumentException('Func is a required preset option.');
		}
		
		if(!in_array(strtolower($preset['func']), $this->allowedFunctions)) {
			throw new \InvalidArgumentException(sprintf("Specified function can only be one of the following: %s", implode(", ", $this->allowedFunctions)));
		}
	}
	
	/**
	 * Run transcode, transforming contents of a text-based file.
	 */
	public function transcodeFile(File $file, Preset $preset, $outFilePath) {
		
		$function = $preset->get('func');
		
		file_put_contents($outFilePath, $function(file_get_contents($file->getFilename())));
		
		return new File($outFilePath);
	}

}