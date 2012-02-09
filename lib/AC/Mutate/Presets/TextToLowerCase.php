<?php

namespace AC\Mutate\Presets;
use AC\Mutate\Preset;

class TextToLowerCase extends Preset {
	protected $name = "text_to_lower";
	protected $description = "Transforms all text in a file into lower case.";
	protected $requiredAdapter = 'php_text';

	protected function buildDefinition() {
		$def = new PresetDefinition;
		
		//configure the definition used by the transcoder
		$def
			->allowExtensions(array('html','txt','md','markdown','textile','json','xml'))
			->inheritOutputExtension();

		return $def;
	}

	public function configure() {
		$this->setOptions(array(
			'func' => 'strtolower'
		));
	}

}