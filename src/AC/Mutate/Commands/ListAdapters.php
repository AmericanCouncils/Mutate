<?php

namespace AC\Mutate\Commands;
use AC\Mutate\Application\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class ListAdapters extends Command
{
    protected $name = "list:adapters";
    protected $description = "Lists registered adapters by their name and class.";

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');

        $output->writeln("Available Adapters:");

        foreach ($this->getTranscoder()->getAdapters() as $adapter) {
            $output->writeln($formatter->formatBlock(sprintf(
                "\"%s\"  (%s): %s",
                $adapter->getName(),
                $adapter->getKey(),
                $formatter->formatBlock($adapter->getDescription(), 'info')
            ), 'comment'));
        }

        return true;
    }

}
