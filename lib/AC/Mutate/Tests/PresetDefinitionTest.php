<?php

namespace AC\Mutate\Tests;
use \AC\Mutate\Preset\PresetDefinition;

include_once __DIR__."/../../../../vendor/.composer/autoload.php";

class PresetDefinitionTest extends \PHPUnit_Framework_TestCase {

	public function testInstantiate() {
		$d = new PresetDefinition;
		$this->assertNotNull($d);
		$this->assertTrue($d instanceof PresetDefinition);
	}
	
	public function testGetDefaults() {
		$d = new PresetDefinition;
		$this->assertFalse($d->getAllowedInputExtensions());
		$this->assertFalse($d->getRejectedInputExtensions());
		$this->assertFalse($d->getRejectedOutputExtensions());
		$this->assertFalse($d->getAllowedOutputExtensions());
		$this->assertTrue($d->inhertOutputExtension());
		$this->assertFalse($d->allowDirectoryInput());
		$this->assertFalse($d->allowDirectoryOutput());
		$this->assertFalse($d->allowDirectoryCreation());

		$this->assertSame(0755, $d->getDirectoryCreationMode());
		$this->assertSame(0644, $d->getFileCreationMode());
		$this->assertSame('file', $d->getInputType());
		$this->assertSame('file', $d->getOutputType());
		$this->assertTrue($d->acceptsExtension('.mp4'));
	}
	
	public function testSetOutputType() {
		$d = new PresetDefinition;
		$d->setOutputType('directory');
		$this->assertTrue($d->allowDirectoryOutput());
		$this->assertTrue($d->allowDirectoryCreation());
	}
	
	public function setAndGetAllowedInputExtensions() {
		$d = new PresetDefiniton;
		$expected = array('mp3','mp4');
		$this->assertFalse($d->getAllowedInputExtensions());
		$d->setAllowedInputExtensions($expected);
		$this->assertSame($expected, $d->getAllowedInputExtensions());
	}

	public function setAndGetRejectedInputExtensions() {
		$d = new PresetDefiniton;
		$expected = array('mp3','mp4');
		$this->assertFalse($d->getRejectedInputExtensions());
		$d->setRejectedInputExtensions($expected);
		$this->assertSame($expected, $d->getRejectedInputExtensions());
	}

	public function setAndGetAllowedOutputExtensions() {
		$d = new PresetDefiniton;
		$expected = array('mp3','mp4');
		$this->assertFalse($d->getAllowedOutputExtensions());
		$d->setAllowedOutputExtensions($expected);
		$this->assertSame($expected, $d->getAllowedOutputExtensions());
	}

	public function setAndGetRejectedOutputExtensions() {
		$d = new PresetDefiniton;
		$expected = array('mp3','mp4');
		$this->assertFalse($d->getRejectedOutputExtensions());
		$d->setRejectedOutputExtensions($expected);
		$this->assertSame($expected, $d->getRejectedOutputExtensions());
	}

	public function testAcceptsExtension1() {
		$d = new PresetDefinition;
		$d->setAllowedInputExtensions(array('mov','mp4'));
		$this->assertTrue($d->acceptsExtension('mov'));
		$this->assertTrue($d->acceptsExtension('mp4'));
		$this->assertFalse($d->acceptsExtension('wmv'));
	}

	public function testAcceptsExtension2() {
		$d = new PresetDefinition;
		$d->setRejectedInputExtensions(array('mov','mp4'));
		$this->assertFalse($d->acceptsExtension('mov'));
		$this->assertFalse($d->acceptsExtension('mp4'));
		$this->assertTrue($d->acceptsExtension('wmv'));
	}
	
	public function testAcceptsInputFile() {
		
	}
}