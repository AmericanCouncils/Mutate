<?php

namespace AC\Mutate\Application;
use AC\Component\Transcoding\File;
use AC\Component\Transcoding\Event\TranscodeEvent;
use AC\Component\Transcoding\Event\TranscodeEvents;
use \Symfony\Component\Console\Output\Output;
use \Symfony\Component\Console\Helper\HelperSet;
use \Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This listener is created by the Application and registered with the Transcoder just before running a command.  This provides an easy way to get output
 * to the command line for any actions performed by the Transcoder.
 */
class CliOutputSubscriber implements EventSubscriberInterface
{
    private $startTime;
    private $output;
    private $helperSet;

    public static function getSubscribedEvents()
    {
        return array(
            TranscodeEvents::BEFORE => array('onTranscodeStart'),
            TranscodeEvents::AFTER => array('onTranscodeComplete'),
            TranscodeEvents::ERROR => array('onTranscodeFailure')
        );
    }

    /**
     * Write to output that a process has started.
     */
    public function onTranscodeStart(TranscodeEvent $e)
    {
        $inpath = $e->getInputFile()->getRealPath();
        $presetKey = $e->getPreset()->getKey();

        $formatter = $this->getFormatter();
        $msg = sprintf(
            "Starting transcode of file %s with preset %s ...",
            $formatter->formatBlock($inpath, 'info'),
            $formatter->formatBlock($presetKey, 'info'));

        $this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));
        $this->startTime = microtime(true);
    }

    /**
     * Write to output that a process has completed.
     */
    public function onTranscodeComplete(TranscodeEvent $e)
    {
        $outpath = $e->getOutputFile()->getRealPath();

        $totalTime = microtime(true) - $this->startTime;
        $formatter = $this->getFormatter();
        $msg = sprintf(
            "Transcode completed in %s ms.",
            $formatter->formatBlock(($totalTime * 1000), 'info')
        );
        $this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));

        $msg = sprintf(
            "Created new file %s",
            $formatter->formatBlock($outpath, 'info')
        );
        $this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));
    }

    /**
     * Write to output that a process has failed.
     */
    public function onTranscodeFailure(TranscodeEvent $e)
    {
        $inpath = $e->getInputFile()->getRealpath();
        $errorMsg = $e->getException()->getMessage();

        $formatter = $this->getFormatter();
        $msg = sprintf(
            "Transcode of %s failed!  Message: %s",
            $formatter->formatBlock($inpath, 'info'),
            $formatter->formatBlock($errorMsg, 'error')
        );

        $this->getOutput()->writeln($formatter->formatBlock($msg, 'comment'));
    }

    protected function getFormatter()
    {
        return $this->helperSet->get('formatter');
    }

    public function setHelperSet(HelperSet $set)
    {
        $this->helperSet = $set;
    }

    public function setOutput(Output $output)
    {
        $this->output = $output;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
