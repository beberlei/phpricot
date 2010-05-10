<?php

class PHPricot_Nodes_BrokenCloseElement extends PHPricot_Nodes_Node
{
    private $name = null;

    function __construct($name) {
        $this->name = $name;
    }

    public function toHtml()
    {
        return '</' . $this->name . '>';
    }

    public function toText()
    {
        return '';
    }
}