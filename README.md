# About #

Mutate is a file transcoding tool abstraction library.  It provides a common interface for tools which transcode files from one format to another.  Several tools (and common presets that use them) are provided with the library, but others can be registered or defined on the fly from your own code.

# Installation #

If you are using the library as a component in another framework or plugin, then there isn't much to set up.  The Mutate code is organized according to PSR-0 standards, so its namespace (`AC\Mutate`) can be registered however you handle autoloading in your framework of choice.

On the other hand, if you want to use Mutate as a stand-alone app for transcoding files via the command-line, some dependencies are required.  You can easily install these dependencies by running the install script included in `/bin` or manually running [Composer](http://packagist.org/) from the Mutate root.  See below:
		
	# go to Mutate root
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

# Basic Usage #

The library is meant to be plugged into, and extended by, other libraries and frameworks.  However, it can also be used as is.  In order for all of the default presets and adapters to work, however, your system must have `ffmpeg` and `imagemagick` or `php-gd2` installed.  Their installation won't be covered in this documentation (yet).

To use a script to simply transcode a file from one format to another, given a preset, you can use the *mutate* script found in the `/bin` directory.

	$> cd /path/to/mutate_root

	# convert image format
	$> php bin/mutate transcode /path/to/test/file.jpg image_to_gif /path/to/output/file.gif
	
	# convert video (if the output file path is not provided, mutate will automatically choose a filename for you in the same directory)
	$> php bin/mutate transcode /path/to/test/file.wmv mp4_720
	
	# run a transcode job
	$> php bin/mutate transcode:job /path/to/test/uploaded.wmv job_name

# Implementation Details & Example Usage #

Mutate consists of several parts.  The first is the transcoder which unifies the transcode process.  It provides the glue through which various adapters, presets and transcoding jobs are registered and can interact in order to transcode files consistently and safely.

## Transcoder ##

The Transcoder does the work of unifying the transcoding process.  What exactly it does when transcoding a file is determined by the presets, adapters and jobs which are registered.

### Usage ###

Using the transcoder by its self is simple, as it has no dependencies.  It can accept presets/adapters/jobs from anywhere - those may have dependencies.

	$transcoder = new AC\Mutate\Transcoder;
	
	// ... register presets, adapters & jobs ... 
	
	//transcode one file using a preset
	$newFile = $transcoder->transcode($inputFilePath, $nameOfPreset, $outputFilePath);
	
	//transcode a file with a specific adaptor and options
	$newFile = $transcoder->transcodeWithAdapter($inputFilePath, $adapterName, array(
		/* options */
	));
	
	//transcode one file using a job (could result in many files depending on the job definition), returns an array of files (only one entry if only one file was created)
	$files = $transcoder->transcodeWithJob($inputFilePath, $nameOfJob);
	
	
## Adapters ##

Adapters are wrappers for a pre-existing toolset which does the real work for any file conversion/manipulation.  Technically these adapters can be anything.  Common examples are `ffmpeg` for audio/video manipulation and ImageMagick or GD2 for image manipulation in PHP.  By default, the library provides `Adapter` implementations for those tools just listed, but others could be easily added.

### Registering an adapter ###

Adapters can be fairly simple, or quite complex.  The adapters included in the library do not have external dependencies which aren't provided by the Library (aside from requiring certain tools be installed on the server).  However, other adapters may require external PHP dependencies and special set-up.  It is beyond the scope of the library to handle this.
	
	//build your custom adapter
	$adapter = new MyCoolAdapter(/* inject any dependencies */);
	$transcoder->registerAdapter($adapter);

	//register adapters provided with the library
	$transcoder->registerAdapter(new FFmpegAdapter);
	$transcoder->registerAdapter(new ImageMagickAdapter);
	
### Writing an adapter ###

All adapters should function the same way - they should simply take an input file object, a string output path, and a hash of arguments for use during the transcode process.  Generally, adapters aren't used directly, but the `transcoder` will call them with arguments defined in a preset, testing for valid input based on the preset definition, and valid output once the transcoded file is created.  Below is an example shell for a custom adapter.

	<?php
	namespace My\Cool\Library;
	use \AC\Mutate\Adapter;
	use \AC\Mutate\File;
	
	class FooAdapter extends Adapter {
		protected $name = 'foo';
		
		public function transcodeFile(File $inFile, $outFilePath, $options = array()) {
			
			//do actual transcode process, however that needs to happen for this adapter
			
			//return a new file instance for the created file
			return new File($outFilePath);
		}
	}
	
### Implementing command-line tools ###

Many file conversion tools are available as command line executables.  Writing code to make executing command line processes safe and consistent accross environments has already been done well with the `Symfony\Process` component, which is provided with this library.  If you want to implement a tool that requires using the command line, we highly recommend using this library rather than writing extra code.

For example, the FFmpeg adapter uses the `Symfony\Process` component to actually execute its command line process.  See the excerpt below:

	// /src/Adapters/FFmpegAdapter.php
	$proc = new \Symfony\Component\Process\Process($command);
	$result = $proc->run(function ($buffer) {
		//push output to cli
	});
	return $result;

## Presets ##

Presets help streamline the transcode process by bundling together common options and requirements into one package.  Several presets are provided with the library for common types of file conversions using popular tools.

### Registering a preset ###

Presets shouldn't have dependencies, since they are really just a mechanism for bundling options which will be passed to an adapter.

	//inline preset
	$transcoder->registerPreset(new \AC\Mutate\Preset('preset_name', 'Required\Adapter\Class', array(/* options */) ));
	
	//pre-defined preset
	$transcoder->registerPreset(new Mp4_HD_720Preset);

### Writing a preset ###

A preset con be declared in two ways.  You may create one by instantiating the preset class, passing it the required options, or you could extend the base `Preset` class.  The library provides many presets which extend the base `Preset` class, to make them more portable.  Presets require two main parts, the first is the actual preset options, which will be passed to the adapter, and the second is a `FileHandlerDefinition` instance, which standardizes what the accepted input/output formats can be.  For example, check out the FFmpeg preset for generating 720 mp4 videos:

	TODO: paste example preset when finalized

## FileHandlerDefinition instances ##

Both Adapters and Presets can specify `FileHandlerDefintion` instances.  These objects are used to validate input files before a process is run, and a resulting output file if created.  The Transcoder uses the `FileHandlerDefinition` to handle input and output in a standardized way.  `FileHandlerDefinition` instances can set restrictions on allowed or rejected input extensions, mime types, mime encodings, for both input and output files.

The FileHandlerDefinition instances are also used by the Transcoder to assemble an output file path, which will be passed to an adapter, if none was provided to the transcoder when running a job.

## Jobs ##

A `Job` is a complex grouping of presets which perform multiple transcoding actions in one request.  It requires a little extra setup, but can make repetive tasks much easier to manage.  Jobs can apply multiple presets to one input file, or branch off and create several output files with one input.  For example, when optimizing videos for web delivery, you may need to transcode an uploaded video into several different formats of varying quality, and create several image thumbnails.  By defining a job classes which leverages several presets, you can define and register all of these actions in one spot.

### Registering a job ###

	TODO

### Writing a job ###

	TODO
	
### Running a job ###

	TODO

# Roadmap #

We will keep track of where we are, and where we're headed, in the development process here.  This library is in the earliest stages of development, so everything documented in this file is subject to change.

Todo list:

* Finalize FileHandlerDefinition and test
* Finalize Preset
* Flesh out Adapter
* Finish Transcoder
* Unit tests in `/tests` for above objects with PHPUnit
* add batch commands, which take directory/patterns
* Implement job definitions
	* Allow chained presets on an output file
	* Allow creation of multiple output files
	* Questions:
		* Allow dependencies or not?
		* Is this an extension of a preset conceptually?  Probably...

# Contributing #

Contributers are certainly welcome.  This library is being worked on as part of a specific project, though we want to make it available as a standalone component because we think it could have value for the community at large.  That said, if you would like to help, feel free to fork, modify, and submit pull requests.  But before spending too much time on it, please get in contact with Evan Villemez (see `composer.json` for contact info) to avoid any duplication of effort!