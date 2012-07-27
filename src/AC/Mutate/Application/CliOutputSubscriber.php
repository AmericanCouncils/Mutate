<?php

namespace AC\Mutate\Application;
use AC\Component\Transcoding\File;
use AC\Component\Transcoding\Event\MessageEvent;
use AC\Component\Transcoding\Event\TranscodeEvent;
use AC\Component\Transcoding\Event\TranscodeEvents;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
            TranscodeEvents::MESSAGE => 'onMessage',
            TranscodeEvents::BEFORE => 'onTranscodeStart',
            TranscodeEvents::AFTER => 'onTranscodeComplete',
            TranscodeEvents::ERROR => 'onTranscodeFailure',
        );
    }
    
    /**
     * Write any messages received by an adapter
     */
    public function onMessage(MessageEvent $e)
    {
        $formatter = $this->getFormatter();
        $adapterKey = $e->getAdapter()->getKey();
        $level = $e->getLevel();
        $message = $e->getMessage();
        
        $match = '/\r\n?/';
        
        //check if the message has weird formatting before trying to format it (currently a hack to avoid segmentation faults)
        if (!preg_match($match, $message)) {
            $msg = sprintf(
                "%s (%s): %s",
                $formatter->formatBlock($adapterKey, 'info'),
                $formatter->formatBlock($level, 'comment'),
                $message
            );
        } else {
            $msg = sprintf(
                "%s (%s): %s",
                $adapterKey,
                $level,
                preg_replace('/\r\n?/', '', $message)
            );
        }
        
        $this->getOutput()->writeln($msg);
    }

    /**
     * Write to output that a process has started.
     */
    public function onTranscodeStart(TranscodeEvent $e)
    {
        $inpath = $e->getInputPath();
        $presetKey = $e->getPreset();

        $formatter = $this->getFormatter();
        $msg = sprintf(
            "Starting transcode of file %s with preset %s ...",
            $formatter->formatBlock($inpath, 'info'),
            $formatter->formatBlock($presetKey, 'info')
        );

        $this->getOutput()->writeln($msg);
        $this->startTime = microtime(true);
    }

    /**
     * Write to output that a process has completed.
     */
    public function onTranscodeComplete(TranscodeEvent $e)
    {
        $outpath = $e->getOutputPath();

        $totalTime = microtime(true) - $this->startTime;
        $formatter = $this->getFormatter();
        $msg = sprintf(
            "Transcode completed in %s seconds.",
            $formatter->formatBlock(round($totalTime, 4), 'info')
        );
        $this->getOutput()->writeln($msg);

        $msg = sprintf(
            "Created new file %s",
            $formatter->formatBlock($outpath, 'info')
        );
        $this->getOutput()->writeln($msg);
    }

    /**
     * Write to output that a process has failed.
     */
    public function onTranscodeFailure(TranscodeEvent $e)
    {
        $inpath = $e->getInputPath();
        $errorMsg = $e->getException()->getMessage();

        $formatter = $this->getFormatter();
        $msg = sprintf(
            "Transcode of %s failed!  Message: %s",
            $formatter->formatBlock($inpath, 'info'),
            $formatter->formatBlock($errorMsg, 'error')
        );

        $this->getOutput()->writeln($msg);
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
