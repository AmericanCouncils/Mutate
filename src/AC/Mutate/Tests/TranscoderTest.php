<?php

namespace AC\Mutate\Tests;

use AC\Mutate\Transcoder;
use AC\Component\Transcoding\Adapter;

class TranscoderTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $t = new Transcoder;
        $this->assertNotNull($t);
        $this->assertTrue($t instanceof Transcoder);
    }

    public function testGetPhpTextAdapter()
    {
        $t = new Transcoder;
        $a = $t->getAdapter('php_text');
        $this->assertTrue($a instanceof Adapter);
        $this->assertSame('php_text', $a->getKey());
    }

    /*
    public function testGetHandbrake()
    {
        $t = new Transcoder;
        $a = $t->getAdapter('handbrake');
        $this->assertTrue($a instanceof Adapter);
        $this->assertSame('handbrake', $a->getKey());
    }
    */
}
