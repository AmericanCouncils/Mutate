# About #

Mutate is a file transcoding tool abstraction library.  It provides a common interface for tools which transcode files from one format to another.  Several tools (and common presets that use them) are provided with the library, but others can be registered or defined on the fly from your own code.

*This library is under heavy development.  APIs are subject to change without notice until the version reaches 1.0.0-alpha.1.*

## Contributing ##

Contributers are certainly welcome.  This library is being worked on as part of a specific project, though we want to make it available as a standalone component because we think it could have value for the community at large.  That said, if you would like to help, feel free to fork, modify, and submit pull requests.  But before spending too much time on it, please get in contact with Evan Villemez (see `composer.json` for contact info) to avoid any duplication of effort!

## Current status ##

We'll keep brief updates here to help people avoid duplication of effort.  You can see a more detailed TODO list and roadmap at the end of this document.

### Commands ###

Currently only 2 application commands work: `status` and `transcode`.  The others are under development, or will remain as placeholders until the Job related APIs are finalized.

### Adapters/Presets ###

Only one is provided right now, and it's just a proof-of-concept to demonstrate end-to-end usage of the library.  The biggest current task is to get adapters and presets implemented for popular tools.

### Active tasks ###

Evan is working on the core library and the commands that use it.

Jay is working specifically on Handbrake and its presets at the moment.

**What we need:**

* FFmpeg adapters / presets
* ImageMagick adapter(s) / presets
	* Note, since ImageMagick actually has a native PHP api, it probably makes most since to have several "adapters" geared towards certain tasks that can be easily parameterized. Such as:
		* ImageFormatConversionAdapter
		* ImageEffectsAdapter
* Other tools, suggestions?

# Installation #

If you are using the library as a component in another framework or plugin, then there isn't much to set up.  The Mutate code is organized according to [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) standards, so its namespace (`AC\Mutate`) can be registered however you handle autoloading in your framework of choice.

On the other hand, if you want to use Mutate as a stand-alone application for transcoding files via the command-line, some dependencies are required.  You can easily install these dependencies by running the install script included in `/bin` or manually running [Composer](http://packagist.org/) from the Mutate root.  See below:
		
	# go to Mutate root (the directory where this file is located)
	$> cd /path/to/mutate_root
	
	$> php bin/install

If that didn't work, try running Composer manually:

	# run ONE of the following:
	$> wget http://getcomposer.org/composer.phar
	# OR
	$> curl -O http://getcomposer.org/composer.phar
	
	# run the install
	$> php composer.phar install
	
You can see if the installation worked by running the following:

	php bin/mutate
	
If the install worked, you will see a list of available commands.  If not, probably errors. :)

To see which presets/adapters provided with the library are usable on your system, run the following:

	php bin/mutate status

Also, if you want to make usage of the `mutate` command available from anywhere, you can symlink the `bin/mutate` script into anywhere on your include path, such as `/usr/bin`.

# Basic Standalone Usage #

The library is meant to be plugged into, and extended by, other libraries and frameworks.  However, it can also be used as is.  In order for all of the default presets and adapters to work, however, your system must have `ffmpeg`, `handbrake`, and `imagemagick` installed.  Their installation won't be covered in this documentation (in the future we'll have documentation about these and other relevant technologies on the github wiki).

To simply transcode a file from one format to another, given a preset, you can use the *mutate* script found in the `/bin` directory.

	$> cd /path/to/mutate_root

	# convert image format
	$> php bin/mutate transcode /path/to/test/file.jpg image_to_gif /path/to/output/file.gif
	
	# convert video (if the output file path is not provided, mutate will automatically choose a filename for you in the same directory)
	$> php bin/mutate transcode /path/to/test/file.wmv mp4_720
	
	# run a transcode job (a grouping of multiple presets)
	$> php bin/mutate transcode:job /path/to/test/uploaded.wmv html5
	
## Not-quite-standalone Usage ##

The lines in the example above invoke the stand-alone app, which runs commands that use the Transcoder.  You can use the Transcoder by its self in your own code, which is detailed in the following the sections.  However, you can also use the stand-alone app in your own code if need-be.  When run in the manner described in the next code example, exceptions will not be caught - it's up to your code to handle them properly.

```php

//instantiate it, which will automatically register the presets/adapters/jobs provided with the library
$app = new \AC\Mutate\Application\Application;
	
//run the command as if it were being run directly from the command line
$status = $app->runCommand(sprintf("transcode %s %s %s", $inFilePath, $presetName, $outFilePath));

//get the output buffer, which in this case is a simple array of logged messages
$output = $app->getOutput();
	
//your transcoded file
$file = new \SplFileObject($outFilePath);
	
//alternatively, you could get the Transcoder created by the Application directly:
$transcoder = $app->getTranscoder();
$transcoder->transcodeWithPreset(/* options */);

```
# Implementation Details & Example Usage #

If you intend to plug Mutate into another framework, or extend it in any way, then knowing the details about how it's built will help.  The core library doesn't actually have any dependencies - the dependencies that are installed as part of the install process are there to make the library usable as a standalone project, and to provide some tools for specific transcoding adapters to leverage.  If you just want to use/extend the core library directly, then the code found in the `src/AC/Mutate` and `src/AC/Mutate/Exception` directories is all you really need.

The core Mutate library consists of several parts.

1. The first is the `Transcoder` class which unifies the transcode process.  It provides the glue through which various adapters, presets and transcoding jobs are registered and can interact in order to transcode files consistently and safely.

2. Second, there are `Adapter` classes which are plugins that receive standardized input, provide some logic to transcode a file, and return some standardized output.

3. Third are the `Preset` classes, which provide groupings of options for the `Adapter` to use when implementing its transcode logic.

4. Fourth are `Files` which is a thin extension of PHP's standard `SplFileObject` class.  These, in conjunction with `Preset` instances, are what `Adapters` take as input.  If the `Adapter` returns a file, it should also be an instance of `AC\Mutate\File`.

5. Fifth are `FileHandlerDefinition` instances.  These can be specified by Adapters, as well as Presets, and define what types of files are allowed as both input and output.  These instances are used internally by the `Transcoder` to ensure valid input/output and to assist in building a valid output file path if none is specified, or to catch an invalid path if provided.

6. Last, there are `Job` classes.  Jobs are complex groupings of presets.  For example, if you want to transcode multiple files from one input file, or apply multiple presets to one file, that type of interaction can be specified in a `Job` class, which you can then run the same as you would any other preset.

Below you will see basic example usage and implementation of each the items mentioned above.

## Transcoder ##

The Transcoder does the work of standardizing the transcoding input and output.  What exactly it does when transcoding a file is determined by the registered presets, adapters and jobs.

### Usage ###

Using the Transcoder by its self is simple, as it has no dependencies.  It can accept presets/adapters/jobs from anywhere, some of which may have their own dependencies if necessary.

	$transcoder = new AC\Mutate\Transcoder;
	
	// ... register presets, adapters & jobs ... 
	$transcoder->registerAdapter(new MyCustomFFmpegAdapter);
	$transcoder->registerPreset(new WebmHDPreset);
	$transcoder->registerJob(new MyHtml5VideoJob);
	
	//transcode one file using a preset
	$newFile = $transcoder->transcodeWithPreset($inputFilePath, 'webm-hd', $outputFilePath);
	
	//transcode a file with a specific adaptor and options
	$newFile = $transcoder->transcodeWithAdapter($inputFilePath, 'custom-ffmpeg', array(
		/* options */
	));
	
	//transcode one file using a job (could result in many files depending on the job definition), returns an array of files (only one entry if only one file was created)
	//note this is subject to change, jobs have not been implemented
	$files = $transcoder->transcodeWithJob($inputFilePath, 'html5_video');
	
	
## Adapters ##

Adapters are wrappers for a pre-existing toolset which does the real work for any file conversion/manipulation.  Technically these adapters can be anything.  Common examples are `ffmpeg` for audio/video manipulation and ImageMagick for image manipulation in PHP.  By default, the library provides `Adapter` implementations for several commonly used tools, including those just mentioned.

### Registering an adapter ###

Adapters can be fairly simple, or quite complex.  The adapters included in the library do not have external dependencies which aren't provided by the library (aside from requiring certain tools be installed on the system).  However, other adapters may require external PHP dependencies and special set-up.  It is beyond the scope of the library to handle this.
	
	//build your custom adapter
	$adapter = new MyCoolAdapter(/* inject any dependencies */);
	$transcoder->registerAdapter($adapter);

	//register adapters provided with the library
	$transcoder->registerAdapter(new FFmpegAdapter);
	$transcoder->registerAdapter(new ImageFormatConverterAdapter);
	$transcoder->registerAdapter(new ImageEffectsAdapter);
	
### Writing an adapter ###

All adapters receive input in the same way - they simply take an input file object, a string output path, and a `Preset` instance for use during the transcode process.  Generally, adapters aren't used directly, but the `Transcoder` will call passing along registered presets, and testing for valid input/output based on the preset definition.  Below is an example template for a very simple custom adapter.  For more detailed documentation on writing an adapter, see the `README.md` in `adapters/`.

	<?php
	use \AC\Mutate\Adapter;
	use \AC\Mutate\File;
	use \AC\Mutate\Preset;
	
	class FooAdapter extends Adapter {
		protected $name = 'foo';
		protected $description = "A made-up adapter for documentation purposes.";
		
		public function transcodeFile(File $inFile, Preset $preset, $outFilePath) {
			
			//do actual transcode process, however that needs to happen for this adapter
			
			//return a new file instance for the created file
			return new File($outFilePath);
		}
	}
	
### Implementing command-line tools ###

Many file conversion tools are available as command line executables.  Writing code to make executing command line processes safe and consistent accross environments has already been done well with the `Symfony\Process` component, which is provided with this library.  If you want to implement a tool that requires using the command line, we highly recommend using this library rather than writing custom code.  Read more on the `Symfony\Process` component [here](https://github.com/symfony/Process).

For example, the FFmpeg and Handbrake adapters use the `Symfony\Process` component to actually execute its command line process.  The general flow goes something like the following:
	
	//method of an adapter class
	public function transcodeFile(File $inFile, Preset $preset, $outFilePath) {
		// ... parse the $preset object to assemble a command string in $commandString ...
		
		//use the Process component to build a process instance with the command string
		$process = new \Symfony\Component\Process\Process($commandString);
		
		//if this could be a long-running process, be sure to increase the timeout limit accordingly
		$process->setTimeout(3600);

		//pass an anonymous function to the process so the adapter can get output as it occurs
		$result = $process->run(function ($type, $buffer) {
			if($type === 'err') {
				//throw an exception, depending on the error
			} else {
				//do something else with the output, whatever that is, maybe append to a status/log file if available
			}
		});

		//check for error status return
		if(!$proc->isSuccessful()) {
			throw new \RuntimeException($proc->getExitCodeText());
		}
		
		return new File($outputFilePath);
	}

## Presets ##

Presets help streamline the transcode process by bundling together common options and requirements into one package.  Several presets are provided with the library for common types of file conversions using popular tools.

For more specific documentation and a usable template, see the `README.md` in `presets/`.

### Registering a preset ###

Presets shouldn't have dependencies, since they are really just a mechanism for bundling options which will be passed to an adapter.  You can declare/register presets in two ways:

	//instantiate inline preset
	$transcoder->registerPreset(new \AC\Mutate\Preset('preset_name', 'required_adapter_name', array(/* preset options */), array(/* FileHandlerDefinition options */)));
	
	//pre-defined preset which extends the Preset class above and defines it's settings internally
	$transcoder->registerPreset(new Mp4_HD_720Preset);

### Writing a preset ###

A preset can be declared in two ways.  You may create one by instantiating the preset class, passing it the required options, or you could extend the base `Preset` class.  The library provides many presets which extend the base `Preset` class, to make them easy to work with.  Presets require two main parts, the first is the actual preset options, which will be passed to the adapter, and the second is a `FileHandlerDefinition` instance, which standardizes what the accepted input/output formats can be.  For example, check out the Handbrake preset for generating 720 mp4 videos:

	TODO: paste example preset when finalized

## FileHandlerDefinition instances ##

Both Adapters and Presets can specify `FileHandlerDefintion` instances to restrict accepted types of input and output files.  The Transcoder uses the `FileHandlerDefinition` instances to handle input and output in a standardized manner.  `FileHandlerDefinition` instances can set restrictions on allowed or rejected input extensions, mime types, mime encodings, and other properties.

The `FileHandlerDefinition` instances are also used by the Transcoder to assemble an output file path, which will be passed to an adapter, if none was provided to the transcoder when running a job.

By default, all `Adapter` and `Preset` classes will return `FileHandlerDefinition` instances for both input and output files which will receive files of any format.

## Jobs ##

A `Job` is a complex grouping of presets which perform multiple transcoding actions in one request.  It requires a little extra setup, but can make repetive tasks much easier to manage.  Jobs can apply multiple presets to one input file, or branch off and create several output files given one input.  For example, when optimizing videos for web delivery, you may need to transcode an uploaded video into several different formats of varying quality, and create several image thumbnails.  By defining a job classes which leverage other presets, you can define and register all of these actions in one location, ensuring each individual action is handled as thoroughly as possible.

### Registering a job ###

	TODO

### Writing a job ###

	TODO
	
### Running a job ###

	TODO

# Changes and Versioning #

We increment versions following the [Semantic Versioning](http://semver.org/) specification.

Important public API changes will be tracked in the *CHANGELOG.md* file once the project reaches `1.0.0-alpha.1` status.  Until that point, major API changes could happen at any time, so you'll have to look at the code documentation, or the README docs (which may or may not be up-to-date, but should be).

## Version Roadmap ##

* For 0.7.0            - You are here
* For 0.8.0            - implement jobs, finalize API, unit test them
* For 0.9.0            - implement remaining job commands
* For 1.0.0-alpha.1    - implement finalize and test default adapters/presets/jobs

# Tests #

Tests are written using `[PHPUnit](http://www.phpunit.de/manual/3.6/en/installation.html)`.  To run them, make sure `PHPUnit` is installed, `cd` to this directory, and run the `phpunit` command.  The tests depend on the autoload file generated by `composer` during the install process.  So, if you haven't run the install described towards the beginning, the tests won't run either.  Assuming the tests run, if they don't pass, it's a problem. :)

# Todo List #

We will keep track of where we are in regards to specific tasks here.

Todo list:

* Unit test Transcoder
* Implement jobs
	* Allow chained presets on one output file
	* Allow creation of multiple output files
	* Questions:
		* Treat this as an extension of a preset?  Probably...
* Commands:
	* Implement job commands
* Create Adapters:
	* Handbrake
	* FFmpeg
	* mencoder (?)
	* various ImageMagick adapters
	* GD2 adapters?  Maybe make presets for these tools interchangeable?
* Implement common presets for above adapters
