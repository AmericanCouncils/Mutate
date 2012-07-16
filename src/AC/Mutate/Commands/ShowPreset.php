<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class ShowPreset extends Command
{
    protected $name = "show:preset";
    protected $description = "Show details for a given preset.";

    protected function onConfigure()
    {
        $this->addArgument('preset_key', InputArgument::REQUIRED, "Key of preset to view.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');

        //get the preset
        $preset = $this->getTranscoder()->getPreset($input->getArgument('preset_key'));

        //write details to console
        $output->writeln(sprintf("Name: %s", $formatter->formatBlock('"'.$preset->getName().'"', 'info')));
        $output->writeln(sprintf("Key: %s", $formatter->formatBlock($preset->getKey(), 'info')));
        $output->writeln(sprintf("Class: %s", $formatter->formatBlock(get_class($preset), 'info')));
        $output->writeln(sprintf("Description: %s", $formatter->formatBlock($preset->getDescription(), 'info')));
        $output->writeln(sprintf("Requires Adapter: %s", $formatter->formatBlock($preset->getRequiredAdapter(), 'info')));
        $output->writeln("Options:");
        foreach ($preset as $key => $val) {
            $output->writeln(sprintf(
                "%s: %s",
                $formatter->formatBlock($key, 'comment'),
                $formatter->formatBlock($val, 'info')
            ));
        }
        $output->writeln("JSON Options String:");
        $output->writeln($formatter->formatBlock(json_encode($preset->getOptions()), 'info'));

        return true;
    }
}
