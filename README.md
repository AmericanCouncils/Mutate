# Mutate #

Mutate is a file transcoding CLI application.  It provides a nice command line interface built on top of the [AC Transcoding](http://github.com/AmericanCouncils/Transcoding/) library.

*This app is under heavy development.  It is developed in sync with the Transcoding library.*

## Installation ##

Right now the only way to install is via `composer`.  Try these commands:

    cd </path/to/mutate_root>
    php composer.phar install

Once the dependencies are installed, you'll need to check the settings `mutate.conf.php` to make sure they work on your system.  Mutate provides wrappers for other programs
which may need to be installed independently on your system.

## Basic Standalone Usage ##

Try `mutate list` to see a list of all available commands.

To see what available adapters (and presets for those adapters) you have, run:

	$> php bin/mutate status

To simply transcode a file from one format to another, given a preset, you can use the *mutate* script found in the `/bin` directory.

	$> cd /path/to/mutate_root

	# convert image format
	$> php bin/mutate transcode /path/to/test/file.jpg image_to_gif /path/to/output/file.gif
	
	# convert video (if the output file path is not provided, mutate will automatically choose a filename for you in the same directory)
	$> php bin/mutate transcode /path/to/test/file.wmv mp4_720
	
	# run a transcode job (a grouping of multiple presets)
	$> php bin/mutate transcode:job /path/to/test/uploaded.wmv html5
	
## Not-quite-standalone Usage ##

Mutate provides it's own transcoder, which accepts an array of configuration and automatically loads adapters, presets and jobs depending on that configuration.  You can use this Transcoder in your code in place of the base `AC\Component\Transcoding\Transcoder` if you wish.

``` php
//build the Mutate transcoder yourself (it automatically registers all adapters/presets/jobs included with the ac/transcoding component, depending on the configuration you pass it)
$transcoder = new AC\Mutate\Transcoder(array(
    //include any custom config for the underlying adapters, or logging, here
));
```

# Tests #

You need PHPUnit to run them.

# Todo List #

See `TODO.md`
