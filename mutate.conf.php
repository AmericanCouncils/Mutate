<?php

return array(
    //enable/disable logging
    'mutate.log.enabled' => true,

    //path on filesystem to log file
    'mutate.log.path' => __DIR__.'/logs/mutate.log',

    //which level of messages to log, see () for more
    'mutate.log.level' => Monolog\Logger::INFO,

    //whether or not the handbrake adapter is enabled
    'transcoder.handbrake.enabled' => true,

    //path to handbrake executable on filesystem, if enabled
    'transcoder.handbrake.path' => "/usr/bin/HandBrakeCLI",

    //process timeout in seconds for handbrake, null means no timeout
    'transcoder.handbrake.timeout' => null,

    //whether or not ffmpeg adapter is enabled
    'transcoder.ffmpeg.enabled' => true,

    //path to ffmpeg executable on filesystem, if enabled
    'transcoder.ffmpeg.path' => "/usr/bin/ffmpeg",

    //process timeout in seconds for ffmpeg, null means no timeout
    'transcoder.ffmpeg.timeout' => null
);
