<?php

namespace AC\Mutate\Application;
use \AC\Mutate\File;
use \AC\Mutate\Preset;
use \AC\Mutate\TranscodeEventListener;
use \Symfony\Component\Console\Output\Output;
use \Symfony\Component\Console\Helper\HelperSet;

class Listener extends TranscodeEventListener {
	private $startTime;
	private $output;
	private $helperSet;
	
	public function onTranscodeStart(File $inFile, Preset $preset, $outputFilePath) {
		$formatter = $this->getFormatter();
		$msg = sprintf(
			"Starting transcode of file %s with preset %s ...",
			$formatter->formatBlock($inFile->getRealPath(), 'info'),
			$formatter->formatBlock($preset->getKey(), 'info'));
			
		$this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));
		$this->startTime = microtime(true);
	}

	public function onTranscodeComplete(File $inFile, Preset $preset, File $outFile) {
		$totalTime = microtime(true) - $this->startTime;
		$formatter = $this->getFormatter();
		$msg = sprintf(
			"Transcode completed in %s ms.",
			$formatter->formatBlock(($totalTime * 1000), 'info')
		);
		$this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));
		
		$msg = sprintf(
			"New file %s created.",
			$formatter->formatBlock($outFile->getRealPath(), 'info')
		);
		$this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));
	}
	
	public function onTranscodeFailure(File $inFile, Preset $preset, $outputFilePath, \Exception $e) {
		$formatter = $this->getFormatter();
		$msg = sprintf(
			"Transcode of %s failed!",
			$formatter->formatBlock($inFile->getRealPath(), 'info')
		);
			
		$this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));
	}

	protected function getFormatter() {
		return $this->helperSet->get('formatter');
	}

	public function setHelperSet(HelperSet $set) {
		$this->helperSet = $set;
	}

	public function setOutput(Output $output) {
		$this->output = $output;
	}
		
	public function getOutput() {
		return $this->output;
	}
}