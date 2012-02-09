# Additional Jobs #

If you intend to use this library in stand-alone mode on your system, then you may add job classes into this directory.  The `mutate` script, which is provided with this library, will automatically scan this directory to add any jobs defined here.  It will add the jobs here in addition to the default jobs provided with the library.

## Naming Conventions ##

Do NOT namespace files in this directory.  Name all files according to the class names, include only one class per file, use the `.php` extension.  If jobs do not adhere to this standard, they won't be recognized by the `mutate` script.  Example:

File name: 

	FooJob.php

Contents:

	<?php
	
	use \AC\Mutate\Job;
	
	class FooJob extends Job {
		// ... code ... //
	}
