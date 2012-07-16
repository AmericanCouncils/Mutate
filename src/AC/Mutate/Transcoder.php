<?php

namespace AC\Mutate;

use AC\Component\Transcoding\Transcoder as BaseTranscoder;
use Pimple;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
/**
 * The Transcoder internally has a dependency injection container for loading adapters, and a logger for events.
 *
 * @package Mutate
 * @author Evan Villemez
 */
class Transcoder extends BaseTranscoder
{
    protected $container;

    /**
     * Constructor can take an array of configuration for use in the dependency injection container.
     * When built, it automatically registers services for adapters provided with the Transcoding library,
     * as well as a logger.
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        //build custom DIC
        $this->container = new Pimple;

        //register adapters/adapters/jobs
        $this->registerDefaultServices();
        $this->registerDefaultPresets();
        $this->registerDefaultJobs();

        //register custom configuration last (in order to override defaults)
        foreach ($config as $key => $val) {
            $this->container[$key] = $val;
        }

        //if logging is enabled register monolog subscriber
        if (isset($this->container['logger'])) {
            $this->addSubscriber(new TranscodeLogSubscriber($this->container));
        }
    }

    /**
     * Register default adapter services, can be overriden from configuration.
     *
     * @return void
     */
    protected function registerDefaultServices()
    {
        //default logger service
        $this->container['logger'] = $this->container->share(function($c) {
            return new Logger(new StreamHandler($c['log.path'], $c['log.level']));
        });

        //demo phptext adapter
        $this->container['adapter.php_text'] = $this->container->share(function($c){
            return new \AC\Component\Transcoding\Adapters\PhpText;
        });

        //handbrake adapter
        $this->container['adapter.handbrake'] = $this->container->share(function($c) {
            return new \AC\Component\Transcoding\Adapters\HandbrakeAdapter($c['handbrake.path']);
        });

    }

    /**
     * Register default presets
     *
     * @return void
     */
    protected function registerDefaultPresets()
    {
        $this->registerPreset(new \AC\Component\Transcoding\Presets\TextToLowerCase());
    }

    /**
     * Register default jobs
     *
     * @return void
     */
    protected function registerDefaultJobs()
    {

    }

    /**
     * Overrides getAdapter to check the container first.
     *
     * {@inheritdoc}
     */
    public function getAdapter($name)
    {
        $serviceKey = 'adapter.'.$name;
        if (!$this->hasAdapter($name)) {
            $this->registerAdapter($this->container[$serviceKey]);
        }

        return parent::getAdapter($name);
    }

    /**
     * Overrides getAdapters to check container for any adapters by prefix of `adapter.`, and loads them.
     *
     * {@inheritdoc}
     */
    public function getAdapters()
    {
        $adapters = array();
        foreach ($this->container as $key => $val) {
            $exp = explode($key);
            if ($exp[0] === 'adapter') {
                $adapters[$exp[1]] = $this->container[$key];
            }
        }

        return $adapters;
    }

    /**
     * Shortcut to registering a shared adapter service for public use
     *
     * @param  string $name
     * @param  mixed  $callable
     * @return void
     */
    public function registerAdapterService($name, $callable)
    {
        $key = 'adapter.'.ltrim("adapter.", $name);
        $this->container[$key] = $this->container->share($callable);
    }
}
