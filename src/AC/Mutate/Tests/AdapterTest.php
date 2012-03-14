<?php

namespace AC\Mutate\Tests;
use \AC\Mutate\Adapter;

class AdapterTest extends \PHPUnit_Framework_TestCase {
	
	public function testInstantiate() {
		$a = new Mock\DummyAdapter;
		$this->assertNotNull($a);
		$this->assertTrue($a instanceof Adapter);
	}
	
	
	public function testVerify() {
		$a = new Mock\DummyAdapter;
		$this->assertTrue($a->verify());
		$this->assertFalse($a->getVerificationError());
		
		$b = new Mock\InvalidDummyAdapter;
		$this->assertFalse($b->verify());
		$this->assertSame("Adapter broken.", $b->getVerificationError());
	}
	
	public function testGetKeyNameAndDescription() {
		$a = new Mock\DummyAdapter;
		$this->assertSame("test_adapter", $a->getKey());
		$this->assertSame("Test Adapter", $a->getName());
		$this->assertSame("Test description.", $a->getDescription());
		
		$b = new Mock\InvalidDummyAdapter;
		$this->assertSame("bad_test_adapter", $b->getKey());
		$this->assertSame("bad_test_adapter", $b->getName());
		$this->assertFalse($b->getDescription());
	}
	
//	public function test
}