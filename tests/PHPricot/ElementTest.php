<?php

class PHPricot_ElementTest extends PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $tag = new PHPricot_Nodes_Element('p', array());
        $this->assertEquals('p', $tag->name);
    }

    public function testToHtml()
    {
        $tag = new PHPricot_Nodes_Element('p', array('class' => 'foo'));
        $tag->childNodes[] = new PHPricot_Nodes_Element('i', array());

        $this->assertEquals('<i>', $tag->html());
    }

    public function testGetAttr()
    {
        $tag = new PHPricot_Nodes_Element('p', array('id' => 'foo'));

        $this->assertNull($tag->attr('class'));
        $this->assertEquals('foo', $tag->attr('id'));
    }

    public function testSetAttr()
    {
        $tag = new PHPricot_Nodes_Element('p', array('id' => 'foo'));
        $tag->attr('id', 'bar')->attr('class', 'baz');

        $this->assertEquals('baz', $tag->attr('class'));
        $this->assertEquals('bar', $tag->attr('id'));
    }

    public function testRemoveAttr()
    {
        $tag = new PHPricot_Nodes_Element('p', array('id' => 'foo'));
        $tag->removeAttr('id');

        $this->assertNull($tag->attr('id'));
    }

    public function testAddClass()
    {
        $tag = new PHPricot_Nodes_Element('p', array());
        $tag->addClass('foo')->addClass('bar');

        $this->assertEquals('foo bar', $tag->attr('class'));
    }

    public function testRemoveClass()
    {
        $tag = new PHPricot_Nodes_Element('p', array());
        $tag->addClass('foo')->addClass('bar')->removeClass('foo');

        $this->assertEquals('bar', $tag->attr('class'));
    }

    public function testHasClass()
    {
        $tag = new PHPricot_Nodes_Element('p', array());

        $this->assertFalse($tag->hasClass('foo'));
        $tag->addClass('foo');
        $this->assertTrue($tag->hasClass('foo'));
    }
}