<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class TranscodeJob extends Command {
	protected $name = "transcode:job";
	protected $description = "Transcodes an input file using a specific job.";
	
	protected function onConfigure() {
		$this->addArgument('inFile', InputArgument::REQUIRED, "String path to input file.");
		$this->addArgument('job', InputArgument::REQUIRED, "Name of job to use.");
		$this->addArgument('outFile', InputArgument::OPTIONAL, "String path to output file.  If not provided, will be determined automatically by the job.", false);
	}
	
    protected function execute(InputInterface $input, OutputInterface $output) {
		throw new \RuntimeException("Command not yet implemented.");
		
	}
}
