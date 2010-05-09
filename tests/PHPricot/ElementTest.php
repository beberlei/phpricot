<?php

if (!function_exists('__autoload')) {
    function __autoload($class)
    {
        require_once __DIR__ . "/../../lib/" . str_replace("_", "/", $class) . ".php";
    }
}

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

        $this->assertEquals('<i>', $tag->innerHtml());
    }
}