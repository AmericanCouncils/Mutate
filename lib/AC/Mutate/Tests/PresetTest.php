<?php

namespace AC\Mutate\Tests;
use \AC\Mutate\File;
use \AC\Mutate\Preset;
use \AC\Mutate\FileHandlerDefinition;

include_once __DIR__."/../../../../vendor/.composer/autoload.php";

class PresetTest extends \PHPUnit_Framework_TestCase {
	
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
	
	public function testSetGetHasRemoveOption() {
		$p = new Preset('name', 'adapter');
		$this->assertFalse($p->has('foo'));
		$this->assertSame('bar', $p->get('foo','bar'));
		$p->set('foo', 'baz');
		$this->assertTrue($p->has('foo'));
		$this->assertSame('baz', $p->get('foo', 'bar'));
		$p->remove('foo');
		$this->assertFalse($p->has('foo'));
		$this->assertSame('bar', $p->get('foo','bar'));
	}
	
	public function testSetGetHasRemoveOptionAsArray() {
		$p = new Preset('foo', 'bar');
		$this->assertFalse(isset($p['foo']));
		$this->assertNull($p['foo']);
		$p['foo'] = 'bar';
		$this->assertTrue(isset($p['foo']));
		$this->assertSame('bar', $p['foo']);
		unset($p['foo']);
		$this->assertFalse(isset($p['foo']));
		$this->assertNull($p['foo']);
	}
	
	public function testSetGetHasRemoveWhenLocked() {
		$p = new Preset('name', 'adapter');
		$p->set('foo', 'bar')->lock();
		$this->assertTrue($p->has('foo'));
		$this->assertSame('bar', $p->get('foo', 'baz'));
		$p->remove('foo');
		$this->assertTrue($p->has('foo'));
		$this->assertSame('bar', $p->get('foo', 'baz'));
		$p->set('foo', 'bazzz');
		$this->assertTrue($p->has('foo'));
		$this->assertSame('bar', $p->get('foo', 'baz'));
	}
	
	public function testSetOptions() {
		$p = new Preset('name', 'adapter');
		$p->setOptions(array(
			'foo' => 'bar',
			'baz' => false,
		));
		
		$this->assertSame('bar', $p['foo']);
		$this->assertFalse($p->get('baz', true));
	}
	
	public function testGetNameAdapterAndDescription() {
		$p = new Preset('name','adapter');
		$this->assertSame('name', $p->getName());
		$this->assertSame('adapter', $p->getRequiredAdapter());
		$this->assertSame("No description provided.", $p->getDescription());
	}
	
	public function testGetInputDefinition() {
		$p = new Preset('name', 'adapter');
		$d = $p->getInputDefinition();
		$this->assertNotNull($d);
		$this->assertTrue($d instanceof FileHandlerDefinition);
	}

	public function testGetOutputDefinition() {
		$p = new Preset('name', 'adapter');
		$d = $p->getOutputDefinition();
		$this->assertNotNull($d);
		$this->assertTrue($d instanceof FileHandlerDefinition);
	}
	
	public function testAcceptsInputFile1() {
		$p = new DummyPreset;
		$this->assertTrue($p->acceptsInputFile(new File(__FILE__)));
	}

	public function testAcceptsInputFile2() {
		$p = new DummyPreset;
		$this->assertFalse($p->acceptsInputFile(new File(__DIR__)));
	}
	
	public function testAcceptsOutputFile1() {
		$p = new DummyPreset;
		$this->assertTrue($p->acceptsOutputFile(new File(__FILE__)));
	}

	public function testAcceptsOutputFile2() {
		$p = new DummyPreset;
		$this->assertFalse($p->acceptsOutputFile(new File(__DIR__)));
	}

	public function testGenerateOutputPath1() {
		$f = new File(__FILE__);
		$p = new DummyPreset;
		$expectedPath = substr($f->getRealPath(), 0, -4).".".$p->getName().".php";
		$this->assertSame($expectedPath, $p->generateOutputPath($f));
	}
	
	public function testGenerateOutputPath2() {
		$f = new File(__FILE__);
		$p = new DummyPreset;
		$expectedPath = '/tmp/test.php';
		$this->assertSame($expectedPath, $p->generateOutputPath($f, $expectedPath));
	}
	
	public function testGenerateOutputPath3() {
		$f = new File(__FILE__);
		$p = new DummyPreset;
		$outputPath = __DIR__;
		$expectedPath = substr($f->getRealPath(), 0, -4).".".$p->getName().".php";
		$this->assertSame($expectedPath, $p->generateOutputPath($f, $outputPath));
	}
	
	public function testGenerateOutputPath4() {
		
	}
}

class DummyPreset extends Preset {
	protected $name = 'test_preset';
	protected $requiredAdapter = "adapter_name";
	
	
}

class InvalidDummyPreset extends Preset {}
