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

    public function testRemoveJQuery()
    {
        $query = new PHPRicot_Query('<div class="container">
  <div class="hello">Hello</div>
  <div class="goodbye">Goodbye</div>
</div>');

        $query->search('.hello')->remove();

        $expected = <<<ETT
<div class="container">
  
  <div class="goodbye">Goodbye</div>
</div>
ETT;
        $this->assertEquals($expected, $query->toHtml());
    }

    public function testReplaceWithJQuery()
    {
        $query = new PHPricot_Query('<div class="container">
  <div class="inner first">Hello</div>
  <div class="inner second">And</div>
  <div class="inner third">Goodbye</div>
</div>');

        $query->search('.second')->replaceWith('<h2>New heading</h2>');

        $expected = <<<ETT
<div class="container">
  <div class="inner first">Hello</div>
  <h2>New heading</h2>
  <div class="inner third">Goodbye</div>
</div>
ETT;
        $this->assertEquals($expected, $query->toHtml());
    }

    public function testNext()
    {
        $query = new PHPricot_Query('<ul><li class="first">Foo</li><li>Bar</li><li>Baz</li></ul>');
        $query->search('li.first')->next()->addClass('second')->next()->addClass('third');

        $this->assertEquals(
            '<ul><li class="first">Foo</li><li class="second">Bar</li><li class="third">Baz</li></ul>',
            $query->toHtml()
        );
    }

    public function testPrev()
    {
        $query = new PHPricot_Query('<ul><li>Bar</li><li class="last">Baz</li></ul>');
        $query->search('li.last')->prev()->addClass('previous');

        $this->assertEquals(
            '<ul><li class="previous">Bar</li><li class="last">Baz</li></ul>',
            $query->toHtml()
        );
    }

    public function testParent()
    {
        $query = new PHPricot_Query('<ul><li>Bar</li><li>Baz</li></ul>');
        $query->search('li')->parent()->addClass('foo');

        $this->assertEquals(
            '<ul class="foo"><li>Bar</li><li>Baz</li></ul>',
            $query->toHtml()
        );
    }

    public function testEmptyChildren()
    {
        $query = new PHPricot_Query('<ul><li>Bar</li><li class="last">Baz</li></ul>');
        $query->search('ul')->emptyChildren();

        $this->assertEquals('<ul></ul>', $query->toHtml());
    }

    public function testBefore()
    {
        $query = new PHPricot_Query('<div class="container">
  <h2>Greetings</h2>
  <div class="inner">Hello</div>
  <div class="inner">Goodbye</div>
</div>');
        $query->search('.inner')->before('<p>Test</p>');

        $expected = <<<ETT
<div class="container">
  <h2>Greetings</h2>
  <p>Test</p><div class="inner">Hello</div>
  <p>Test</p><div class="inner">Goodbye</div>
</div>
ETT;

        $this->assertEquals($expected, $query->toHtml());
    }

    public function testAfter()
    {
        $query = new PHPricot_Query('<div class="container">
  <h2>Greetings</h2>
  <div class="inner">Hello</div>
  <div class="inner">Goodbye</div>
</div>');
        $query->search('.inner')->after('<p>Test</p>');

        $expected = <<<ETT
<div class="container">
  <h2>Greetings</h2>
  <div class="inner">Hello</div><p>Test</p>
  <div class="inner">Goodbye</div><p>Test</p>
</div>
ETT;

        $this->assertEquals($expected, $query->toHtml());
    }

    public function testCountable()
    {
        $query = new PHPricot_Query('<div class="container">
  <h2>Greetings</h2>
  <div class="inner">Hello</div>
  <div class="inner">Goodbye</div>
</div>');

        $this->assertEquals(1, count($query));
    }

    public function testIteratorAggregate()
    {
        $query = new PHPricot_Query('<p>foo</p><p>bar</p><p>baz</p>');

        $count = 0;
        foreach ($query AS $node) {
            $count++;
            $this->assertType('PHPricot_Nodes_Element', $node);
        }
    }
}
