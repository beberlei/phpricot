<?php

class PHPricot_ToTextTest extends PHPUnit_Framework_TestCase
{
    public function testHeader1()
    {
        $query = new PHPricot_Query('<h1>Foo</h1><p>bar <a href="#foo">baz</a></p>');

        $expected = <<<ETT

*****************************************************************
Foo
*****************************************************************

bar baz
ETT;
        $this->assertEquals($expected, $query->getDocument()->toText());
    }

    public function testHeader2()
    {
        $query = new PHPricot_Query('<h2>Foo</h2>');

        $expected = <<<ETT

-----------------------------------------------------------------
Foo
-----------------------------------------------------------------
ETT;
        $this->assertEquals($expected, $query->getDocument()->toText());
    }


    public function testHeader3()
    {
        $query = new PHPricot_Query('<h3>Foo</h3>');

        $expected = <<<ETT
Foo
-----------------------------------------------------------------
ETT;
        $this->assertEquals($expected, $query->getDocument()->toText());
    }

    public function testList()
    {
        $query = new PHPricot_Query('<ul><li>Foo</li><li>Bar</li></ul>');

        $expected = <<<ETT
 * Foo
 * Bar
ETT;
        $this->assertEquals($expected, $query->getDocument()->toText());
    }
}