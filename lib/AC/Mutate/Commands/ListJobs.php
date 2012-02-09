<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class ListJobs extends Command {
	protected $name = "list:jobs";
	protected $description = "Lists registered jobs by their name and class.";
	
    protected function execute(InputInterface $input, OutputInterface $output) {
		
	}
}
