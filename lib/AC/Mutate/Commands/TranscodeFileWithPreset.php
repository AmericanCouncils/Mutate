<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class TranscodeFileWithPreset extends Command {
	protected $name = "transcode";
	protected $description = "Transcodes an input file using a preset.";
	
	protected function onConfigure() {
		$this->addArgument('inFile', InputArgument::REQUIRED, "String path to input file.");
		$this->addArgument('preset', InputArgument::REQUIRED, "Name of preset to use.");
		$this->addArgument('outFile', InputArgument::OPTIONAL, "String path to output file.  If not provided, will be determined automatically based on source file.", false);
	}
	
    protected function execute(InputInterface $input, OutputInterface $output) {
		
	}
}
