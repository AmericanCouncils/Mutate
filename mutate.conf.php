<?php

return array(
    'mutate.log.enabled' => true,
    'mutate.log.path' => __DIR__.'/logs/mutate.log',
    'mutate.log.level' => Monolog\Logger::INFO,
    'transcoder.handbrake.enabled' => true,
    'transcoder.handbrake.path' => "/usr/local/bin/HandBrakeCLI",
    'transcoder.ffmpeg.enabled' => false,
    'transcoder.ffmpeg.path' => ""
);
