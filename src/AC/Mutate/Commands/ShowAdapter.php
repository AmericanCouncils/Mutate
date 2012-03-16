<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class ShowAdapter extends Command {
	protected $name = "show:adapter";
	protected $description = "Show details for a given adapter.";
	
	protected function onConfigure() {
		$this->addArgument('adapter_key', InputArgument::REQUIRED, "Key of adapter to view.");
	}
	
    protected function execute(InputInterface $input, OutputInterface $output) {
		$formatter = $this->getHelper('formatter');

		//get the adapter
		$adapter = $this->getTranscoder()->getAdapter($input->getArgument('adapter_key'));
		
		//write details to console
		$output->writeln(sprintf("Name: %s", $formatter->formatBlock('"'.$adapter->getName().'"', 'info')));
		$output->writeln(sprintf("Key: %s", $formatter->formatBlock($adapter->getKey(), 'info')));
		$output->writeln(sprintf("Class: %s", $formatter->formatBlock(get_class($adapter), 'info')));
		$output->writeln(sprintf("Description: %s", $formatter->formatBlock($adapter->getDescription(), 'info')));
		$output->writeln(sprintf("Verified: %s", $formatter->formatBlock($adapter->verify() ? 'yes' : 'no', 'info')));
		$output->writeln("Available Presets:");
		foreach($this->getTranscoder()->getPresets() as $preset) {
			if($preset->getRequiredAdapter() === $adapter->getKey()) {
				$output->writeln(sprintf(
					"%s: %s",
					$formatter->formatBlock($preset->getKey(), 'comment'),
					$formatter->formatBlock($preset->getDescription(), 'info')
				));
			}
		}
		
		return true;
	}
}
