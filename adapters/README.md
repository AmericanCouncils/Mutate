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