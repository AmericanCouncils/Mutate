<?php

namespace AC\Mutate;

abstract class EventListener {

	public function onTranscodeStart(File $inFile, Preset $preset, $outputFilePath) {
		
	}

	public function onTranscodeComplete(File $outFile) {
		
	}
	
	public function onTranscodeFailure(File $inFile, Preset $preset, \Exception $e) {
		
	}
	
}