<?php

return array(
    'mutate.log.enabled' => true,
    'mutate.log.path' => __DIR__.'/logs/mutate.log',
    'mutate.log.level' => Monolog\Logger::INFO,
    'transcoder.handbrake.enabled' => true,
    'transcoder.handbrake.path' => "/usr/bin/HandBrakeCLI",
    'transcoder.handbrake.timeout' => null,
    'transcoder.ffmpeg.enabled' => false,
    'transcoder.ffmpeg.path' => ""
);
