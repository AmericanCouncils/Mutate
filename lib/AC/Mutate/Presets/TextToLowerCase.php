<?php

namespace AC\Mutate\Presets;
use AC\Mutate\Preset;

/**
 * A very simple preset, for a very simple adapter, to illustrate how writing a preset works.
 */
class TextToLowerCase extends Preset {
	protected $name = "text_to_lower";
	protected $description = "Transforms all text in a file into lower case.";
	protected $adapter = 'php_text';

	protected function buildDefinition() {
		$def = new PresetDefinition;
		
		//configure the definition used by the transcoder
		$def
			->setInputExtensionRestrictions(array('html','txt','md','markdown','textile','json','xml'))
			->setInheritOutputExtension(true);

		return $def;
	}

	public function configure() {
		$this->setOptions(array(
			'func' => 'strtolower'
		));
	}
}