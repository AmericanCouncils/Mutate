<?php

namespace AC\Mutate\Tests;
use \AC\Mutate\File;
use \AC\Mutate\Preset;
use \AC\Mutate\FileHandlerDefinition;

include_once __DIR__."/../../../../vendor/.composer/autoload.php";

class FileTest extends \PHPUnit_Framework_TestCase {
	
	public function testInstatiateDynamic1() {
		$p = new Preset('name', 'adapter');
		$this->assertNotNull($p);
		$this->assertTrue($p instanceof Preset);
	}
	
	public function testInstatiateDynamic2() {
        $this->setExpectedException('AC\Mutate\Exception\InvalidPresetException');
		$p = new Preset();
	}
	
	public function testInstatiateDynamic3() {
        $this->setExpectedException('AC\Mutate\Exception\InvalidPresetException');
		$p = new Preset('foo');
	}

	public function testInstantiateExtended1() {
        $this->setExpectedException('AC\Mutate\Exception\InvalidPresetException');
		$p = new InvalidDummyPreset;
	}

	public function testInstantiateExtended2() {
		$p = new DummyPreset;
		$this->assertNotNull($p);
		$this->assertTrue($p instanceof DummyPreset);
	}
	
	
	//TODO: more tests
	
}

class DummyPreset extends Preset {
	protected $name = 'test_preset';
	protected $requiredAdapter = "adapter_name";
	
	
}

class InvalidDummyPreset extends Preset {}