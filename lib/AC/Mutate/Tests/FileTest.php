<?php

namespace AC\Mutate\Tests;
use \AC\Mutate\File;

include_once __DIR__."/../../../../vendor/.composer/autoload.php";

class FileTest extends \PHPUnit_Framework_TestCase {
	
	public function testInstantiate() {
		$f = new File(__FILE__);
		$this->assertNotNull($f);
		$this->assertTrue($f instanceof File);
	}
	
	public function testGetExtension() {
		$f1 = new File(__FILE__);
		$f2 = new File(__DIR__."/test_files/foo");
		$f3 = new File(__DIR__."/test_files/foo.txt");
		$f4 = new File(__DIR__."/test_files/foo.md");
		$f5 = new File(__DIR__."/test_files/foo.mp3");
		$this->assertSame('php', $f1->getExtension());
		$this->assertSame('', $f2->getExtension());
		$this->assertSame('txt', $f3->getExtension());
		$this->assertSame('md', $f4->getExtension());
		$this->assertSame('mp3', $f5->getExtension());
	}
	
	public function testGetMime() {
		$f1 = new File(__FILE__);
		$f2 = new File(__DIR__."/test_files/foo");
		$f3 = new File(__DIR__."/test_files/foo.txt");
		$f4 = new File(__DIR__."/test_files/foo.md");
		$f5 = new File(__DIR__."/test_files/foo.mp3");
		$this->assertSame('text/x-php; charset=us-ascii', $f1->getMime());
		$this->assertSame('text/plain; charset=us-ascii', $f2->getMime());
		$this->assertSame('text/plain; charset=us-ascii', $f3->getMime());
		$this->assertSame('text/plain; charset=us-ascii', $f4->getMime());
		$this->assertSame('text/plain; charset=us-ascii', $f5->getMime());
	}
	
	public function testGetMimeType() {
		$f1 = new File(__FILE__);
		$f2 = new File(__DIR__."/test_files/foo");
		$f3 = new File(__DIR__."/test_files/foo.txt");
		$f4 = new File(__DIR__."/test_files/foo.md");
		$f5 = new File(__DIR__."/test_files/foo.mp3");
		$this->assertSame('text/x-php', $f1->getMimeType());
		$this->assertSame('text/plain', $f2->getMimeType());
		$this->assertSame('text/plain', $f3->getMimeType());
		$this->assertSame('text/plain', $f4->getMimeType());
		$this->assertSame('text/plain', $f5->getMimeType());
	}

	public function testGetMimeEncoding() {
		$f1 = new File(__FILE__);
		$f2 = new File(__DIR__."/test_files/foo");
		$f3 = new File(__DIR__."/test_files/foo.txt");
		$f4 = new File(__DIR__."/test_files/foo.md");
		$f5 = new File(__DIR__."/test_files/foo.mp3");
		$this->assertSame('us-ascii', $f1->getMimeEncoding());
		$this->assertSame('us-ascii', $f2->getMimeEncoding());
		$this->assertSame('us-ascii', $f3->getMimeEncoding());
		$this->assertSame('us-ascii', $f4->getMimeEncoding());
		$this->assertSame('us-ascii', $f5->getMimeEncoding());
	}
}