<?php

class PHPricot_QueryTest extends PHPUnit_Framework_TestCase
{
    public function testAttr()
    {
        $query = new PHPricot_Query("<p></p>");
        $value = $query->search('p')->attr('id', 'bar')->attr('id');

        $this->assertEquals('bar', $value);
        $this->assertEquals('<p id="bar"></p>', $query->toHtml());
    }

    public function testRemoveAttr()
    {
        $query = new PHPricot_Query("<p></p>");
        $query->search('p')->attr('id', 'bar')->removeAttr('id');

        $this->assertEquals("<p></p>", $query->toHtml());
    }

    public function testAddClass()
    {
        $query = new PHPricot_Query('<ul><li>foo</li><li>bar</li></ul>');
        $query->search('li')->addClass('selected');

        $this->assertEquals('<ul><li class="selected">foo</li><li class="selected">bar</li></ul>', $query->toHtml());
    }

    public function testRemoveClass()
    {
        $query = new PHPRicot_Query('<p class="foo baz"></p>');
        $query->search('p')->removeClass('foo');

        $this->assertEquals('<p class="baz"></p>', $query->toHtml());
    }

    public function testToggleClass()
    {
        $query = new PHPRicot_Query('<p class="foo baz"></p>');
        $query->search('p')->toggleClass('foo')->toggleClass('bar');

        $this->assertEquals('<p class="baz bar"></p>', $query->toHtml());
    }

    public function testHasClass()
    {
        $query = new PHPRicot_Query('<p class="foo baz"></p>');
        $this->assertTrue($query->search('p')->hasClass('foo'));
        $this->assertTrue($query->search('p')->hasClass('baz'));
        $this->assertFalse($query->search('p')->hasClass('bar'));
    }

    public function testFirst()
    {
        $query = new PHPricot_Query('<ul><li>foo</li><li>bar</li></ul>');
        $query->search('li')->first()->addClass('bar');

        $this->assertEquals('<ul><li class="bar">foo</li><li>bar</li></ul>', $query->toHtml());
    }
    
    public function testLast()
    {
        $query = new PHPricot_Query('<ul><li>foo</li><li>bar</li></ul>');
        $query->search('li')->last()->addClass('bar');

        $this->assertEquals('<ul><li>foo</li><li class="bar">bar</li></ul>', $query->toHtml());
    }

    public function testAppend()
    {
        $query = new PHPRicot_Query('<body><p>foo</p><p>bar</p></body>');
        $query->search('body')->append('<img src="foo.gif" />');

        $this->assertEquals('<body><p>foo</p><p>bar</p><img src="foo.gif" /></body>', $query->toHtml());
    }

    public function testAppendJQuery()
    {
        $query = new PHPricot_Query('<h2>Greetings</h2>
<div class="container">
  <div class="inner">Hello</div>
  <div class="inner">Goodbye</div>
</div>');
        $query->search('.inner')->append('<p>Test</p>');

        $expected = <<<ETT
<h2>Greetings</h2>
<div class="container">
  <div class="inner">Hello<p>Test</p></div>
  <div class="inner">Goodbye<p>Test</p></div>
</div>
ETT;
        $this->assertEquals($expected, $query->toHtml());
    }

    public function testPrependJQuery()
    {
        $query = new PHPricot_Query('<h2>Greetings</h2>
<div class="container">
  <div class="inner">Hello</div>
  <div class="inner">Goodbye</div>
</div>');
        $query->search('.inner')->prepend('<p>Test</p>');

        $expected = <<<ETT
<h2>Greetings</h2>
<div class="container">
  <div class="inner"><p>Test</p>Hello</div>
  <div class="inner"><p>Test</p>Goodbye</div>
</div>
ETT;
        $this->assertEquals($expected, $query->toHtml());
    }
}
