<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use AC\Mutate\File;

class Scan extends Command {
	protected $name = "scan";
	protected $description = "Show details for a given file.";
	
	protected function onConfigure() {
		$this->addArgument('path', InputArgument::REQUIRED, "Path of file to scan.");
	}
	
    protected function execute(InputInterface $input, OutputInterface $output) {
		$formatter = $this->getHelper('formatter');

		//get the preset
		$file = new File($input->getArgument('path'));
		
		$output->writeln("File details:");
		$output->writeln(sprintf(
			"%s: %s",
			$formatter->formatBlock("Path", 'comment'),
			$formatter->formatBlock($file->getRealPath(), 'info')
		));
		$output->writeln(sprintf(
			"%s: %s",
			$formatter->formatBlock("Type", 'comment'),
			$formatter->formatBlock($file->getType(), 'info')
		));
		if($file->isDir()) {
			$output->writeln(sprintf(
				"%s: %s",
				$formatter->formatBlock("Contained Files", 'comment'),
				$formatter->formatBlock(count($file->getContainedFiles()), 'info')
			));
		}
		
		$output->writeln(sprintf(
			"%s: %s",
			$formatter->formatBlock("Size (bytes)", 'comment'),
			$formatter->formatBlock($file->getSize(), 'info')
		));
		$output->writeln(sprintf(
			"%s: %s",
			$formatter->formatBlock("Extension", 'comment'),
			$formatter->formatBlock($file->getExtension(), 'info')
		));
		$output->writeln(sprintf(
			"%s: %s",
			$formatter->formatBlock("Full Mime", 'comment'),
			$formatter->formatBlock($file->getMime(), 'info')
		));
		$output->writeln(sprintf(
			"%s: %s",
			$formatter->formatBlock("Mime Type", 'comment'),
			$formatter->formatBlock($file->getMimeType(), 'info')
		));
		$output->writeln(sprintf(
			"%s: %s",
			$formatter->formatBlock("Mime Encoding", 'comment'),
			$formatter->formatBlock($file->getMimeEncoding(), 'info')
		));
		
		return true;
	}
}
