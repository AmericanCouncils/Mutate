<?php

namespace AC\Mutate\Tests\Mock;
use AC\Mutate\File;
use AC\Mutate\Preset;
use AC\Mutate\TranscodeEventListener;

class Listener extends TranscodeEventListener {
	public $messages = array();
	
	public function onTranscodeStart(File $inFile, Preset $preset, $outputFilePath) {
		$messages[] = __METHOD__;
	}

	public function onTranscodeComplete(File $inFile, Preset $preset, File $outFile) {
		$messages[] = __METHOD__;
	}
	
	public function onTranscodeFailure(File $inFile, Preset $preset, $outputFilePath, \Exception $e) {
		$messages[] = __METHOD__;
	}
}
