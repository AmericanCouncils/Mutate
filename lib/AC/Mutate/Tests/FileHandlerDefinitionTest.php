<?php

namespace AC\Mutate\Tests;
use \AC\Mutate\FileHandlerDefinition;
use \AC\Mutate\File;

include_once __DIR__."/../../../../vendor/.composer/autoload.php";

class FileHandlerDefinitionTest extends \PHPUnit_Framework_TestCase {

	public function testInstantiate() {
		$d = new FileHandlerDefinition;
		$this->assertNotNull($d);
		$this->assertTrue($d instanceof FileHandlerDefinition);
	}
	
	public function testGetDefaults() {
		$d = new FileHandlerDefinition;
		$this->assertFalse($d->getAllowedInputExtensions());
		$this->assertFalse($d->getRejectedInputExtensions());
		$this->assertFalse($d->getRejectedOutputExtensions());
		$this->assertFalse($d->getAllowedOutputExtensions());
		$this->assertFalse($d->getInheritOutputExtension());
		$this->assertFalse($d->getAllowDirectoryInput());
		$this->assertFalse($d->getAllowDirectoryOutput());
		$this->assertFalse($d->getAllowDirectoryCreation());

		$this->assertSame(0755, $d->getDirectoryCreationMode());
		$this->assertSame(0644, $d->getFileCreationMode());
		$this->assertSame('file', $d->getInputType());
		$this->assertSame('file', $d->getOutputType());
		$this->assertTrue($d->acceptsInputExtension('mp4'));
		$this->assertTrue($d->acceptsOutputExtension('mp4'));
		$this->assertTrue($d->acceptsInputMime('text/plain; charset=us-ascii'));
		$this->assertTrue($d->acceptsOutputMime('text/plain; charset=us-ascii'));
		$this->assertTrue($d->acceptsInputMimeType('text/plain'));
		$this->assertTrue($d->acceptsOutputMimeType('text/plain'));
		$this->assertTrue($d->acceptsInputMimeEncoding('us-ascii'));
		$this->assertTrue($d->acceptsOutputMimeEncoding('us-ascii'));
	}
	
	public function testConstructorSetOptions() {
		$d = new FileHandlerDefinition(array(
			'allowedInputExtensions' => array('html','php','txt'),
			'allowedInputMimeEncodings' => array('us-ascii'),
			'rejectedOutputMimeTypes' => array('text/x-php'),
			'inheritOutputExtension' => true
		));
		
		$this->assertTrue($d->acceptsInputExtension('html'));
		$this->assertFalse($d->acceptsInputExtension('pdf'));
		$this->assertTrue($d->acceptsInputMimeEncoding('us-ascii'));
		$this->assertFalse($d->acceptsInputMimeEncoding('utf-8'));
		$this->assertTrue($d->acceptsOutputMimeType('text/plain'));
		$this->assertFalse($d->acceptsOutputMimeType('text/x-php'));
	}
	
	public function testAcceptInputAndOutputFile1() {
		$f = new File(__FILE__);
		$d = new FileHandlerDefinition;
		$this->assertTrue($d->acceptsInputFile($f));
		$this->assertTrue($d->acceptsOutputFile($f));
	}
	
	public function testAcceptInputAndOutputFile2() {
		$f = new File(__DIR__);
		$d = new FileHandlerDefinition;
		$this->assertFalse($d->acceptsInputFile($f));
		$this->assertFalse($d->acceptsOutputFile($f));
	}

	public function testAcceptInputAndOutputFile3() {
		$f = new File(__DIR__);
		$d = new FileHandlerDefinition;
		$d->setInputType('directory')->setOutputType('directory');
		$this->assertTrue($d->acceptsInputFile($f));
		$this->assertTrue($d->acceptsOutputFile($f));
	}

	public function testAcceptInputAndOutputFile4() {
		$f = new File(__FILE__);
		$d = new FileHandlerDefinition;
		$d->setInputType('directory')->setOutputType('directory');
		$this->assertTrue($d->acceptsInputFile($f));
		$this->assertTrue($d->acceptsOutputFile($f));
	}

	public function testAcceptInputAndOutputFile5() {
		$f = new File(__FILE__);
		$d = new FileHandlerDefinition;
		$d
			->setOutputType('directory')
			->setAllowedInputMimeTypes(array('text/x-php'));
		$this->assertTrue($d->acceptsInputFile($f));
		$this->assertFalse($d->acceptsOutputFile($f));
	}
}