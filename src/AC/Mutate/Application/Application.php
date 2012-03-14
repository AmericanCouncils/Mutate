<?php

namespace AC\Mutate\Application;
use \AC\Mutate\Transcoder;
use \Symfony\Component\Console\Application as BaseApplication;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Main CLI Aplication class which builds a shared instance of the Transcoder and automatically registers commands, adapters, presets and jobs provided with the library.
 */
class Application extends BaseApplication {
	private $transcoder = false;
	
	public function __construct() {
		parent::__construct("Mutate File Transcoder", Transcoder::VERSION);
		
		//build transcoder
		$this->transcoder = $this->buildTranscoder();

		//register default adapters
		foreach($this->getDefaultAdapters() as $adapter) {
			$this->transcoder->registerAdapter($adapter);
		}
		
		//register default presets
		foreach($this->getDefaultPresets() as $preset) {
			$this->transcoder->registerPreset($preset);
		}
		
		//register default jobs
		foreach($this->getDefaultJobs() as $job) {
			$this->transcoder->registerJob($job);
		}

	}
	
	/**
	 * Run a command from string input.  By default will not catch exceptions when run in this manner.
	 * 
	 * This is provided as a convenient way to run commands with the stand-alone application from within another
	 * framework or application, if need-be.
	 *
	 * @param string $string 
	 * @param string $catch 
	 * @return \AC\Mutate\Application\ArrayOutput
	 * @author Evan Villemez
	 */
	public function runCommand($string, $catch = false) {
		$this->setCatchExceptions($catch);
		$input = new \Symfony\Component\Console\Input\StringInput($string);
		$output = new ArrayOutput;
		$result = $this->run($input, $output);
		$output->writeln("Finished with status: ".$result);
		return $output;
	}
	
	/**
	 * Build the shared instance of the Transcoder
	 *
	 * @return AC\Mutate\Transcoder
	 * @author Evan Villemez
	 */
	protected function buildTranscoder() {
		return new Transcoder;
	}
	
	/**
	 * Add commands in /Commands directory to default commands
	 *
	 * @return array
	 */
	protected function getDefaultCommands() {
		$commands = parent::getDefaultCommands();
	
		foreach(scandir(__DIR__."/../Commands") as $item) {
			if(!in_array($item, array('.','..'))) {
				$class = substr("AC\\Mutate\\Commands\\".$item, 0, -4); //get rid of ".php"
				$commands[] = new $class;
			}
		}
	
		return $commands;
	}
	
	/**
	 * Modify the default InputDefinition to add entry for the interactive shell.
	 *
	 * @return Symfony\Component\Console\Input\InputDefinition
	 */
    protected function getDefaultInputDefinition()
    {
		$def = parent::getDefaultInputDefinition();
		$def->addOption(new InputOption('--shell', '-s', InputOption::VALUE_NONE, 'Enter the interactive shell.'));
		return $def;
    }
	
	
	/**
	 * Return array of default adapters provided with the library
	 *
	 * @return array
	 */
	protected function getDefaultAdapters() {
		$items = array();

		foreach(scandir(__DIR__."/../Adapters") as $item) {
			if(strpos($item, '.php')) {
				$class = substr("AC\\Mutate\\Adapters\\".$item, 0, -4); //get rid of ".php"
				$items[] = new $class;
			}
		}
		
		return $items;
	}
	
	/**
	 * Return array of default presets provided with the library
	 *
	 * @return array
	 */
	protected function getDefaultPresets() {
		$items = array();

		foreach(scandir(__DIR__."/../Presets") as $item) {
			if(strpos($item, '.php')) {
				$class = substr("AC\\Mutate\\Presets\\".$item, 0, -4); //get rid of ".php"
				$items[] = new $class;
			}
		}
		
		return $items;
	}
	
	/**
	 * Return array of default jobs provided with the library
	 *
	 * @return array
	 */
	protected function getDefaultJobs() {
		$items = array();

		foreach(scandir(__DIR__."/../Jobs") as $item) {
			if(strpos($item, '.php')) {
				$class = substr("AC\\Mutate\\Jobs\\".$item, 0, -4); //get rid of ".php"
				$items[] = new $class;
			}
		}
		
		return $items;
	}
	
	/**
	 * Check for whether or not to run interactive shell
	 *
	 * @param InputInterface $input 
	 * @param OutputInterface $output 
	 * @return int
	 */
    public function doRun(InputInterface $input, OutputInterface $output) {
		
		//setup and register transcoder listener to give user feedback during any transcode events
		$listener = new Listener;
		$listener->setOutput($output);
		$listener->setHelperSet($this->getHelperSet());
		$this->getTranscoder()->registerListener($listener);

        if (true === $input->hasParameterOption(array('--shell', '-s'))) {
			
            $shell = new Shell($this);
            $shell->run();

            return 0;
        }

        return parent::doRun($input, $output);
	}
	
	/**
	 * Return shared Transcoder instance
	 *
	 * @return AC\Mutate\Transcoder
	 */
	public function getTranscoder() {
		return $this->transcoder;
	}
	
	/**
	 * Set shared transcoder instance
	 *
	 * @param AC\Mutate\Transcoder $t 
	 */
	public function setTranscoder(Transcoder $t) {
		$this->transcoder = $t;
	}

}
