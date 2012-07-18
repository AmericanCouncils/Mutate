<?php

namespace AC\Mutate;

use AC\Component\Transcoding\Event\TranscodeEvent;
use AC\Component\Transcoding\Event\TranscodeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Monolog\Logger;
use Pimple;

/**
 * Uses Monolog to log all transcode events
 *
 * @package Mutate
 * @author Evan Villemez
 */
class TranscodeLogSubscriber implements EventSubscriberInterface
{

    public function __construct(Pimple $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            TranscodeEvents::BEFORE => array('onBeforeTranscode'),
            TranscodeEvents::AFTER => array('onAfterTranscode'),
            TranscodeEvents::ERROR => array('onTranscodeError')
        );
    }

    public function onBeforeTranscode(TranscodeEvent $e)
    {
        $inpath = $e->getInputPath();
        $presetKey = $e->getPreset();
        $outpath = $e->getOutputPath();
        $this->container['logger']->addInfo(sprintf("Beginning transcode of [%s] to [%s] with preset [%s]", $inpath, $presetKey, $outpath));
    }

    public function onAfterTranscode(TranscodeEvent $e)
    {
        $inpath = $e->getInputPath();
        $presetKey = $e->getPreset();
        $outpath = $e->getOutputPath();
        $this->container['logger']->addInfo(sprintf("Finished transcode of [%s] to [%s] with preset [%s]", $inpath, $presetKey, $outpath));
    }

    public function onTranscodeError(TranscodeEvent $e)
    {
        $inpath = $e->getInputPath();
        $presetKey = $e->getPreset();
        $outpath = $e->getOutputPath();
        $exception = $e->getException();
        $this->container['logger']->addError(sprintf("Encountered error [%s] during transcode of [%s] to [%s]", $exception->getMessage(), $inpath, $outpath));
    }

}
