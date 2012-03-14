<?php

namespace AC\Mutate\Tests\Mock;

use AC\Mutate\Adapter;
use AC\Mutate\Preset;
use AC\Mutate\FileHandlerDefinition;

class DummyAdapter extends Adapter {
	protected $key = "test_adapter";
	protected $name = "Test Adapter";
	protected $description = "Test description.";
	
	
	
}