<?php

namespace AC\Mutate\Tests\Mock;

use AC\Mutate\Adapter;
use AC\Mutate\Preset;
use AC\Mutate\FileHandlerDefinition;

class InvalidDummyAdapter extends Adapter {
	protected $key = "bad_test_adapter";

	protected function verifyEnvironment() {
		throw new \Exception("Adapter broken.");
	}

}
