<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class TranscodeFileWithAdapter extends Command {
	protected $name = "transcode:adapter";
	protected $description = "Transcodes an input file using a specific adapter with user-specified options.";
	
	protected function onConfigure() {
		$this->addArgument('inFile', InputArgument::REQUIRED, "String path to input file.");
		$this->addArgument('adapter', InputArgument::REQUIRED, "Name of adapter to use.");
		$this->addArgument('options', InputArgument::REQUIRED, "JSON hash of options to pass to driver.");
		$this->addArgument('outFile', InputArgument::OPTIONAL, "String path to output file.  If not provided, will be determined automatically based on source file.", false);
	}
	
    protected function execute(InputInterface $input, OutputInterface $output) {
		
	}
}
