<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class Status extends Command {
	protected $name = "status";
	protected $description = "Show status of all available adapters, and list usable presets.";
	
    protected function execute(InputInterface $input, OutputInterface $output) {
		$formatter = $this->getHelper('formatter');

		//show adapters
		$adapters = $this->getTranscoder()->getAdapters();
		$output->writeln("Adapter status:");
		if(empty($adapters)) {
			$output->writeln($formatter->formatBlock("No available adapters.", 'info'));
		} else {
			foreach($adapters as $adapter) {
				$msg = $adapter->verify() ? "Verified." : $adapter->getVerificationError();
				$msg = $adapter->verify() ? $formatter->formatBlock($msg, 'info') : $formatter->formatBlock($msg, 'error');
				$output->writeln($formatter->formatBlock($adapter->getName().":      ".$msg, 'comment'));
			}
		}
		
		//show presets
		$output->writeln('');
		$output->writeln("Usable Presets: ");
		
		$presets = $this->getTranscoder()->getPresets();
		$usablePresets = array();
		foreach($presets as $preset) {
			if($this->getTranscoder()->getAdapter($preset->getRequiredAdapter())->verify()) {
				$usablePresets[] = $preset;
			}
		}
		
		if(empty($usablePresets)) {
			$output->writeln($formatter->formatBlock("No usable presets.", 'info'));
		} else {
			foreach($usablePresets as $preset) {
				$output->writeln($formatter->formatBlock($preset->getName()." (".$preset->getKey().")", 'comment').": ".$formatter->formatBlock($preset->getDescription(), 'info'));
			}
		}
		
		return true;
	}
}
