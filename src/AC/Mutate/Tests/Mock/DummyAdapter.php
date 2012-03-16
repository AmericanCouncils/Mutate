<?php

namespace AC\Mutate\Tests\Mock;

use AC\Mutate\Adapter;
use AC\Mutate\Preset;
use AC\Mutate\File;
use AC\Mutate\FileHandlerDefinition;

class DummyAdapter extends Adapter {
	protected $key = "test_adapter";
	protected $name = "Test Adapter";
	protected $description = "Test description.";
	
	public function transcodeFile(File $file, Preset $preset, $outFilePath) {
		
		if(!copy($file->getRealPath(), $outFilePath)) {
			throw new \RuntimeException("Could not copy.");
		}
				
		return new File($outFilePath);
	}
}