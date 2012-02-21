# Additional Presets #

If you intend to use this library in stand-alone mode on your system, then you may add presets classes into this directory.  The Mutate CLI Application, which is provided with this library, will automatically scan this directory to add any presets defined here.  It will add the presets here in addition to the default presets provided with the library.

## Naming Conventions ##

Do NOT namespace files in this directory.  Name all files according to the class names, include only one class per file, use the `.php` extension.  If presets do not adhere to this standard, they won't be recognized and automatically added by the `mutate` script.  Example:

File name: 

	FooPreset.php

Contents:

	<?php
	
	use \AC\Mutate\Preset;
	
	class FooPreset extends Preset {
		// ... code ... //
	}


## Empty Preset Template ##

For your convenience, you can copy/paste the template code below into a new file to start work on a new preset.

	<?php
	//corresponding file name for this class should be "MyNewPreset.php"
	use \AC\Mutate\Preset;
	use \AC\Mutate\FileHandlerDefinition;
	
	class MyNewPreset extends Preset {
		protected $name = 'preset_name';
		protected $requiredAdapter = 'required_adapter_name';
		protected $description = "Provide a human-readable description here.";
		
		public function configure() {
			$this->setOptions(array(
				//specify any key/val arguments to pass to the adapter
			));
		}
		
		protected function buildInputDefinition() {
			$def = new FileHandlerDefinition;

			//if you want to specify restrictions on the types of input files this preset can handle, you can set those restrictions here
			
			return $def FileHandlerDefinition;
		}
		
		protected function buildOutputDefinition() {
			$def = new FileHandlerDefinition;

			//if you want to specify restrictions on the types of output files this preset will allow, you can set those restrictions here
			
			return $def;
		}
	}
	