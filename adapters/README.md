# Additional Adapters #

If you intend to use this library in stand-alone mode on your system, then you may add adapter classes into this directory.  The Mutate CLI Application, which is provided with this library, will automatically scan this directory to add any adapters defined here.  It will add the adapters here in addition to the default adapters provided with the library.

## Naming Conventions ##

Do NOT namespace files in this directory.  Name all files according to the class names, include only one class per file, use the `.php` extension.  If adapters do not adhere to this standard, they won't be recognized by the `mutate` script.  Example:

File name: 

	HeyWatchApiAdapter.php

Contents:

	<?php
	
	use \AC\Mutate\Adapter;
	
	class HeyWatchApiAdapter extends Adapter {
		// ... code ... //
	}
	
	
## Empty Adapter Template ##

For your convenience, you can copy/paste the template code below into a new file to start work on a new adapter.

	<?php
	//corresponding file name for this class should be "MyNewAdapter.php"
	use \AC\Mutate\Adapter;
	use \AC\Mutate\File;
	use \AC\Mutate\Preset;
	use \AC\Mutate\FileHandlerDefinition;
	
	class MyNewAdapter extends Adapter {
		protected $name = 'adapter_name';
		protected $description = "Brief human-readable description of this adapter can do.";
		
		public function transcodeFile(File $inFile, Preset $preset, $outputFilePath) {

			//implement your transcoding logic here, however that needs to be done.  You should not do any extra logic for figuring out
			//a proper output file name, that logic will be taken care of by the transcoder -  always assume you are getting back a properly
			//formed string $outputFilePath to use as the output.
			
			return new File($outputFilePath);
		}
		
		protected function buildInputDefinition() {
			$def = new FileHandlerDefinition;

			//if you want to specify restrictions on the types of input files this adapter can handle, you can set those restrictions here
			
			return $def FileHandlerDefinition;
		}
		
		protected function buildOutputDefinition() {
			$def = new FileHandlerDefinition;

			//if you want to specify restrictions on the types of output files this adapter will allow, you can set those restrictions here
			
			return $def;
		}

		public function validatePreset(Preset $preset) {
			//if you want to specify custom preset validation logic, you can do that here
			//be sure to throw exceptions on failure, otherwise return true on success
			
			return true;
		}
		
		public function verifyEnvironment() {
			//if your adapter needs to run any checks to ensure that it can function in the current environment, for
			//instance, to check whether or not another command-line tool is installed on the system, that logic
			//should be implemented here.  Throw exceptions on failure, return `true` on success.
			
			return true;
		}
	}