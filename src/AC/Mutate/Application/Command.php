<?php

namespace AC\Mutate\Application;
use \Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Base Mutate Command provides easy access to the Transcoder instance, which the MutateApplication should have created.
 *
 * @package default
 * @author Evan Villemez
 */
abstract class Command extends BaseCommand
{
    protected $name = false;
    protected $description = "No description given.";
    protected $help = "No help information given.";
    protected $aliases = array();

    protected function configure()
    {
        if (!$this->name) {
            throw new \LogicException("Commands require the {name} property to be defined.");
        }

        $this
            ->setDefinition($this->defineArguments())
            ->setName($this->name)
            ->setDescription($this->description)
            ->setHelp($this->help)
            ->setAliases($this->aliases);
        $this->onConfigure();
    }

    /**
     * For extending commands to implement custom functionality
     */
    protected function onConfigure()
    {
    }

    /**
     * Convenience method for use defining arguments during construct
     *
     * @return array
     */
    protected function defineArguments()
    {
        return array();
    }

    /**
     * Convenience method to get the shared transcoder instance created by the application during construct.
     *
     * @return AC\Mutate\Transcoder
     */
    protected function getTranscoder()
    {
        return $this->getApplication()->getTranscoder();
    }

}
