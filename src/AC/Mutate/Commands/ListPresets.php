<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class ListPresets extends Command
{
    protected $name = "list:presets";
    protected $description = "Lists registered presets by their name and class, organized by adapter.";

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');

        $output->writeln("Available Presets:");

        foreach ($this->getTranscoder()->getPresets() as $preset) {
            $output->writeln($formatter->formatBlock(sprintf(
                "\"%s\"  (%s): %s",
                $preset->getName(),
                $preset->getKey(),
                $formatter->formatBlock($preset->getDescription(), 'info')
            ), 'comment'));
        }

        return true;
    }
}
