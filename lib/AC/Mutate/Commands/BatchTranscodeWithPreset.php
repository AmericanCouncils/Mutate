<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class BatchTranscodeWithPreset extends Command {
	protected $name = "transcode:batch";
	protected $description = "Convert files in directory with a preset, optionally specify pattern to match against files.";
	
	protected function onConfigure() {
		
	}
	
    protected function execute(InputInterface $input, OutputInterface $output) {
		
	}
}
