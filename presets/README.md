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
