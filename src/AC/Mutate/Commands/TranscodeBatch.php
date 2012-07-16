<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use AC\Mutate\Transcoder;

class TranscodeBatch extends Command
{
    protected $name = "transcode:batch";
    protected $description = "Use a preset to convert multiple files in a directory matching a given pattern.";
    protected $help = <<<HELP

Note that any glob patterns passed directly from the command line must be wrapped in quotes.  If being run
in interactive mode, the pattern does not have to be wrapped in quotes.

Example Usage:
    <comment>php bin/mutate transcode:batch text_to_lower "./*.txt" /tmp/transcoded_files</comment>

The above command will run the 'text_to_lower' preset on all files with a '.txt' extension in
the current directory, writing the new files into the '/tmp/transcoded_files' directory.

HELP;

    protected function onConfigure()
    {
        $this->addArgument('preset_key', InputArgument::REQUIRED, "Key of preset to use.");
        $this->addArgument('pattern', InputArgument::REQUIRED, "Glob pattern to match files.  Note that no tilde expansion or parameter substitution is done, and the pattern must be wrapped in quotes.");
        $this->addArgument('output_directory', InputArgument::OPTIONAL, "String path to output directory.  If not provided, the current working directory will be used.  New directories will automatically be created.", false);

        //boolean option flags
        $this->addOption('increment', 'i', InputOption::VALUE_NONE, "Numerically increment the output file path if it already exists.");
        $this->addOption('force', 'f', InputOption::VALUE_NONE, "Force transcoder to overwrite any pre-existing files if present.");
        $this->addOption('preserve', 'p', InputOption::VALUE_NONE, "Do not delete any created files on a failed transcode.");
        $this->addOption('skip', 'S', InputOption::VALUE_NONE, "Skip files that can't be transcoded, instead of stopping the process.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //figure out output directory
        $outputDirectory = ($input->getArgument('output_directory')) ? $input->getArgument('output_directory') : getcwd();

        //figure out conflict mode
        $conflictMode = ($input->getOption('increment')) ? Transcoder::ONCONFLICT_INCREMENT : Transcoder::ONCONFLICT_EXCEPTION;
        if ($input->getOption('force')) {
            $conflictMode = Transcoder::ONCONFLICT_DELETE;
        }

        //set dir mode to always create
        $dirMode = Transcoder::ONDIR_CREATE;

        //figure out fail mode
        $failMode = ($input->getOption('preserve')) ? Transcoder::ONFAIL_PRESERVE : Transcoder::ONFAIL_DELETE;

        //get files by glob pattern and run the transcode
        $transcoder = $this->getTranscoder();
        $newFiles = array();
        $errorMessages = array();
        foreach (glob($input->getArgument('pattern')) as $path) {
            try {

                $newFiles[] = $transcoder->transcodeWithPreset($path, $input->getArgument('preset_key'), $outputDirectory, $conflictMode, $dirMode, $failMode);

            } catch (\Exception $e) {
                if ($input->getOption('skip')) {
                    $errorMessages[$path] = $e->getMessage();
                } else {
                    throw $e;
                }
            }
        }

        //write aggregate results to console
        $formatter = $this->getHelper('formatter');
        $output->writeln(sprintf(
            "Process finished.  %s new files created in %s",
            $formatter->formatBlock(count($newFiles), 'info'),
            $formatter->formatBlock($outputDirectory, 'info')
        ));

        //write any errors that were skipped over
        if (!empty($errorMessages)) {
            $output->writeln("The following files could not be transcoded due to errors: ");
            foreach ($errorMessages as $path => $message) {
                $output->writeln(sprintf(
                    "%s: %s",
                    $formatter->formatBlock($path, 'info'),
                    $formatter->formatBlock($message, 'error')
                ));
            }
        }

        return true;
    }
}
