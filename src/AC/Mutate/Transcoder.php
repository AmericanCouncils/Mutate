<?php

namespace AC\Mutate;

use AC\Component\Transcoding\Transcoder as BaseTranscoder;
use Pimple;
use Monolog\Logger;
/**
 * The Transcoder internally has a dependency injection container for loading adapters, and a logger for events.
 *
 * @package Mutate
 * @author Evan Villemez
 */
class Transcoder extends BaseTranscoder
{
    protected $container;

    protected $adapterServices = array();

    /**
     * Constructor can take an array of configuration for use in the dependency injection container.
     * When built, it automatically registers services for adapters provided with the Transcoding library,
     * as well as a logger.
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        //build custom DIC w/ default config
        $this->container = new Pimple;
        $this->container['transcoder.handbrake.enabled'] = false;
        $this->container['transcoder.ffmpeg.enabled'] = false;

        //register custom configuration last (in order to override defaults)
        foreach ($config as $key => $val) {
            $this->container[$key] = $val;
        }

        //register adapters/adapters/jobs
        $this->registerDefaultServices();
        $this->registerDefaultPresets();
        $this->registerDefaultJobs();
    }

    /**
     * Register default adapter services, can be overriden from configuration.
     *
     * @return void
     */
    protected function registerDefaultServices()
    {
        //demo phptext adapter
        $this->registerAdapterService('php_text', function($c) {
            return new \AC\Component\Transcoding\Adapter\PhpText;
        });

        //handbrake adapter
        if ($this->container['transcoder.handbrake.enabled']) {
            $this->registerAdapterService('handbrake', function($c) {
                return new \AC\Component\Transcoding\Adapter\HandbrakeAdapter($c['transcoder.handbrake.path'], $c['transcoder.handbrake.timeout']);
            });
        }

        //ffmpeg adapter
        if ($this->container['transcoder.ffmpeg.enabled']) {
            $this->registerAdapterService('ffmpeg', function($c) {
                return new \AC\Component\Transcoding\Adapter\FFmpegAdapter($c['transcoder.ffmpeg.path'], $c['transcoder.ffmpeg.timeout']);
            });
        }
    }

    /**
     * Register default presets
     *
     * @return void
     */
    protected function registerDefaultPresets()
    {
        $this->registerPreset(new \AC\Component\Transcoding\Preset\TextToLowerCase());

        //if handbrake is enabled, register its presets
        if ($this->container['transcoder.handbrake.enabled']) {
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\AppleTV2Preset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\AppleTVLegacyPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\AppleTVPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\ClassicPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\HighProfilePreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\iPadPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\iPhone4Preset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\iPhoneiPodTouchPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\iPhoneLegacyPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\iPodLegacyPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\iPodPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\NormalPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\Handbrake\UniversalPreset);
        }

        //register ffmpeg presets if it's enabled
        if ($this->container['transcoder.ffmpeg.enabled']) {
            $this->registerPreset(new \AC\Component\Transcoding\Preset\FFmpeg\AudioCompression32kPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\FFmpeg\AudioCompression96kPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\FFmpeg\AudioCompression128kPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\FFmpeg\AudioCompression160kPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\FFmpeg\AudioCompression192kPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\FFmpeg\AudioCompression256kPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\FFmpeg\AudioCompression320kPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\FFmpeg\AviToAnimatedGifPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\FFmpeg\ConvertNonMVideoPreset);
            $this->registerPreset(new \AC\Component\Transcoding\Preset\FFmpeg\SoundFromVideoPreset);
        }
    }

    /**
     * Register default jobs
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
        foreach ($this->adapterServices as $key) {
            $exp = explode(".", $key);
            $adapters[$exp[1]] = $this->container[$key];
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
        $key = 'adapter.'.$name;
        $this->adapterServices[] = $key;
        $this->container[$key] = $this->container->share($callable);
    }
}
