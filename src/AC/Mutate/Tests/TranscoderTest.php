<?php

namespace AC\Mutate\Tests;
use \AC\Mutate\Adapter;
use \AC\Mutate\File;
use \AC\Mutate\Preset;
use \AC\Mutate\FileHandlerDefinition;
use \AC\Mutate\Transcoder;

class TranscoderTest extends \PHPUnit_Framework_TestCase {

	public function tearDown() {
		@unlink(__DIR__."/test_files/test_file.php");
		@unlink(__DIR__."/test_files/test_file.mp3");
		@unlink(__DIR__."/test_files/transcoded/test_file.php");
		@unlink(__DIR__."/test_files/transcoded/test_file.php");
	}
	
	public function testInstantiate() {
		$t = new Transcoder;
		$this->assertNotNull($t);
		$this->assertInstanceOf('\AC\Mutate\Transcoder', $t);
	}
	
	public function testHasRegisterGetAndRemovePreset() {
		$t = new Transcoder;
		$this->assertFalse($t->hasPreset('test_preset'));
		$this->assertSame(0, count($t->getPresets()));
		$t->registerPreset(new Mock\DummyPreset);
		$this->assertTrue($t->hasPreset('test_preset'));
		$this->assertSame(1, count($t->getPresets()));
		$p = $t->getPreset('test_preset');
		$this->assertInstanceOf('AC\Mutate\Tests\Mock\DummyPreset', $p);
		$t->removePreset('test_preset');
		$this->assertFalse($t->hasPreset('test_preset'));
		$this->assertSame(0, count($t->getPresets()));
	}
	
	public function testHasRegisterGetAndRemoveAdapter() {
		$t = new Transcoder;
		$this->assertFalse($t->hasAdapter('test_adapter'));
		$this->assertSame(0, count($t->getAdapters()));
		$t->registerAdapter(new Mock\DummyAdapter);
		$this->assertTrue($t->hasAdapter('test_adapter'));
		$this->assertSame(1, count($t->getAdapters()));
		$p = $t->getAdapter('test_adapter');
		$this->assertInstanceOf('AC\Mutate\Tests\Mock\DummyAdapter', $p);
		$t->removeAdapter('test_adapter');
		$this->assertFalse($t->hasAdapter('test_adapter'));
		$this->assertSame(0, count($t->getAdapters()));
	}
	
	public function testHasRegisterGetAndRemoveListener() {
		$t = new Transcoder;
		$class = 'AC\Mutate\Tests\Mock\Listener';
		$this->assertFalse($t->hasListener($class));
		$t->registerListener(new $class);
		$this->assertTrue($t->hasListener($class));
		$t->removeListener($class);
		$this->assertFalse($t->hasListener($class));
	}
	
/**
 * TODO: Test job methods once API is finalized
 */

	public function testGetAndSetFileCreationMode() {
		$t = new Transcoder;
		$this->assertSame(0644, $t->getFileCreationMode());
		$t->setFileCreationMode(0777);
		$this->assertSame(0777, $t->getFileCreationMode());
		$t->setFileCreationMode("644");
		$this->assertSame(0644, $t->getFileCreationMode());
	}
	
	public function testGetAndSetDirectoryCreationMode() {
		$t = new Transcoder;
		$this->assertSame(0755, $t->getDirectoryCreationMode());
		$t->setDirectoryCreationMode(0777);
		$this->assertSame(0777, $t->getDirectoryCreationMode());
		$t->setDirectoryCreationMode("644");
		$this->assertSame(0644, $t->getDirectoryCreationMode());
	}
	
	public function testTranscodeFileWithPreset1() {
		//start here
	}
	
}