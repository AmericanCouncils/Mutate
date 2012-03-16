<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use AC\Mutate\Transcoder;

class TranscodeAdapter extends Command {
	protected $name = "transcode:adapter";
	protected $description = "Transcodes an input file using a specific adapter with user-specified options.";
	protected $help = <<<HELP

Remember to wrap any json option structures in single quotes, with all keys and string values wrapped in double quotes.  For example:
	<comment>php bin/mutate transcode:adapter ~/Desktop/Foo.txt php_text '{"func":"strtolower"}'</comment>

HELP;
	
	protected function onConfigure() {
		$this->addArgument('inFile', InputArgument::REQUIRED, "String path to input file.");
		$this->addArgument('adapter_key', InputArgument::REQUIRED, "Key of adapter to use.");
		$this->addArgument('options', InputArgument::REQUIRED, "JSON hash of options to pass to driver.");
		$this->addArgument('outFile', InputArgument::OPTIONAL, "String path to output file.  If not provided, will be determined automatically based on source file.", false);

		//boolean option flags
		$this->addOption('increment', 'i', InputOption::VALUE_NONE, "Numerically increment the output file path if it already exists.");
		$this->addOption('force', 'f', InputOption::VALUE_NONE, "Force transcoder to overwrite any pre-existing files if present.");
		$this->addOption('recurse', 'r', InputOption::VALUE_NONE, "Recursively create any needed directories during the transcode process.");
		$this->addOption('preserve', 'p', InputOption::VALUE_NONE, "Do not delete any created files on a failed transcode.");
	}
	
    protected function execute(InputInterface $input, OutputInterface $output) {

		//parse the json structure
		if(!$options = json_decode($input->getArgument('options'), true)) {
			throw new \InvalidArgumentException("JSON structure could not be parsed.");
		}
		
		//figure out conflict mode
		$conflictMode = ($input->getOption('increment')) ? Transcoder::ONCONFLICT_INCREMENT : Transcoder::ONCONFLICT_EXCEPTION;
		if($input->getOption('force')) {
			$conflictMode = Transcoder::ONCONFLICT_DELETE;
		}
		
		//figure out dir mode
		$dirMode = ($input->getOption('recurse')) ? Transcoder::ONDIR_CREATE : Transcoder::ONDIR_EXCEPTION;
		
		//figure out fail mode
		$failMode = ($input->getOption('preserve')) ? Transcoder::ONFAIL_PRESERVE : Transcoder::ONFAIL_DELETE;
		
		//run the transcode
		$inFile = $input->getArgument('inFile');
		$adapter = $input->getArgument('adapter_key');
		$outFile = $input->getArgument('outFile');
		$newFile = $this->getTranscoder()->transcodeWithAdapter($inFile, $adapter, $options, $outFile, $conflictMode, $dirMode, $failMode);
		
		return true;
	}
}
