<?php

namespace AC\Mutate\Application;
use \AC\Mutate\File;
use \AC\Mutate\Preset;
use \AC\Mutate\TranscodeEventListener;
use \Symfony\Component\Console\Output\Output;
use \Symfony\Component\Console\Helper\HelperSet;

/**
 * This listener is created by the Application and registered with the Transcoder just before running a command.  This provides an easy way to get output
 * to the command line for any actions performed by the Transcoder.
 */
class Listener extends TranscodeEventListener {
	private $startTime;
	private $output;
	private $helperSet;
	
	/**
	 * Write to output that a process has started.
	 */
	public function onTranscodeStart(File $inFile, Preset $preset, $outputFilePath) {
		$formatter = $this->getFormatter();
		$msg = sprintf(
			"Starting transcode of file %s with preset %s ...",
			$formatter->formatBlock($inFile->getRealPath(), 'info'),
			$formatter->formatBlock($preset->getKey(), 'info'));
			
		$this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));
		$this->startTime = microtime(true);
	}

	/**
	 * Write to output that a process has completed.
	 */
	public function onTranscodeComplete(File $inFile, Preset $preset, File $outFile) {
		$totalTime = microtime(true) - $this->startTime;
		$formatter = $this->getFormatter();
		$msg = sprintf(
			"Transcode completed in %s ms.",
			$formatter->formatBlock(($totalTime * 1000), 'info')
		);
		$this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));
		
		$msg = sprintf(
			"Created new file %s",
			$formatter->formatBlock($outFile->getRealPath(), 'info')
		);
		$this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));
	}
	
	/**
	 * Write to output that a process has failed.
	 */
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