<?php

return array(
    'log.enabled' => true,
    'log.path' => __DIR__.'/logs/mutate.log',
    'log.level' => Monolog\Logger::INFO,
    'handbrake.path' => "/usr/local/bin/HandBrakeCLI",
    'ffmpeg.path' => ""
);
