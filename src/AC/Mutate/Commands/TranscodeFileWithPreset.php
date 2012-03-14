<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use AC\Mutate\Transcoder;
use AC\Mutate\File;


class TranscodeFileWithPreset extends Command {
	protected $name = "transcode";
	protected $description = "Transcodes an input file using a preset.";
	
	protected function onConfigure() {
		$this->addArgument('inFile', InputArgument::REQUIRED, "String path to input file.");
		$this->addArgument('preset_key', InputArgument::REQUIRED, "Key of preset to use.");
		$this->addArgument('outFile', InputArgument::OPTIONAL, "String path to output file.  If not provided, will be determined automatically based on source file.", false);
//		$this->addOption();
	}
	
    protected function execute(InputInterface $input, OutputInterface $output) {
		$inFile = new File($input->getArgument('inFile'));
		$presetName = $input->getArgument('preset_key');
		$outputPath = $input->getArgument('outFile');
		
		//TODO: update with options for setting conflict/dir/fail modes
		
		$newFile = $this->getTranscoder()->transcodeWithPreset($inFile, $presetName, $outputPath, Transcoder::ONCONFLICT_INCREMENT, Transcoder::ONDIR_CREATE, Transcoder::ONFAIL_DELETE);
		
		return true;
	}
}
