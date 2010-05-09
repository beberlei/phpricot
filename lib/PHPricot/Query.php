<?php
/**
 * PHPricot
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

class PHPricot_Query
{
    private $doc;

    public function __construct($input)
    {
        if (is_string($input)) {
            $parser  = new PHPricot_Parser();
            $doc = $parser->parse($input);
        } else if($input instanceof PHPricot_Document) {
            $doc = $input;
        } else if($input instanceof PHPricot_Nodes_Element) {
            $doc = new PHPricot_Document();
            $doc->childNodes[] = $input;
        } else if(is_array($input)) {
            $doc = new PHPricot_Document();
            $doc->childNodes = $input;
        } else {
            throw new InvalidArgumentException('PHPricot_Query expects an HTML string as argument.');
        }

        $this->doc = $doc;
    }

    public function first()
    {
        return new PHPricot_Query($this->_getFirstMatch());
    }

    public function last()
    {
        $elements = $this->_getChildElements();
        if (count($elements) > 0) {
            return new PHPricot_Query(array_pop($elements));
        } else {
            throw new InvalidArgumentException('No element found, no last element could be selected from that.');
        }
    }

    /**
     * Append inputed nodes as last children of each matched element
     *
     * @param  string|PHPricot_Query|PHPricot_Nodes_Node $input
     * @return PHPricot_Query
     */
    public function append($input)
    {
        if(!($input instanceof PHPricot_Query)) {
            $input = new PHPricot_Query($input);
        }
        $childNodes = $input->getDocument()->childNodes;
        foreach ($this->_getChildElements() AS $element) {
            $element->childNodes = array_merge($element->childNodes, $childNodes);
        }
        return $this;
    }

    public function prepend($input)
    {
        if(!($input instanceof PHPricot_Query)) {
            $input = new PHPricot_Query($input);
        }
        $childNodes = $input->getDocument()->childNodes;
        foreach ($this->_getChildElements() AS $element) {
            $element->childNodes = array_merge($childNodes, $element->childNodes);
        }
        return $this;
    }

    public function emptyChildren()
    {
        foreach ($this->_getChildElements() AS $element) {
            $element->childNodes = array();
        }
        return $this;
    }

    public function html()
    {
        try {
            $first = $this->_getFirstMatch();
            return $first->html();
        } catch(InvalidArgumentException $e) {
            return "";
        }
    }

    public function find($cssSelector)
    {
        return $this->search($cssSelector);
    }

    public function search($cssSelector)
    {
        $cssHandler = new PHPricot_CssParser_EventHandler($this->doc);
        $cssParser = new CssParser($cssSelector, $cssHandler);
        $cssParser->parse();

        return new PHPricot_Query($cssHandler->getMatches());
    }

    public function addClass($name)
    {
        foreach ($this->_getChildElements() AS $element) {
            $element->addClass($name);
        }
        return $this;
    }

    public function hasClass($name)
    {
        foreach ($this->_getChildElements() AS $element) {
            if ($element->hasClass($name)) {
                return true;
            }
        }
        return false;
    }

    public function removeClass($name)
    {
        foreach ($this->_getChildElements() AS $element) {
            $element->removeClass($name);
        }
        return $this;
    }

    public function toggleClass($name)
    {
        foreach ($this->_getChildElements() AS $element) {
            $element->toggleClass($name);
        }
        return $this;
    }

    public function attr($name, $value = null)
    {
        try {
            $element = $this->_getFirstMatch();
            $attributeValue = $element->attr($name, $value);
            if (!$value) {
                return $attributeValue;
            }
        } catch(InvalidArgumentException $e) {

        }
        return $this;
    }

    public function removeAttr($name)
    {
        foreach ($this->_getChildElements() AS $element) {
            $element->removeAttr($name);
        }
        return $this;
    }

    public function __invoke($args)
    {
        if (!isset($args[0])) {
            throw new InvalidArgumentException("PHPricot_Query expects a CSS selector as argument to __invoke().");
        }
        return $this->search($args[0]);
    }

    private function _getChildElements()
    {
        $elements = array();
        foreach ($this->doc->childNodes AS $child) {
            if ($child instanceof PHPricot_Nodes_Element) {
                $elements[] = $child;
            }
        }
        return $elements;
    }

    /**
     * @return PHPricot_Nodes_Element
     */
    private function _getFirstMatch()
    {
        $elements = $this->_getChildElements();
        if (isset($elements[0])) {
            return $elements[0];
        } else {
            throw new InvalidArgumentException('No first element found in the current matched subset.');
        }
    }

    public function toHtml()
    {
        return $this->doc->toHtml();
    }

    public function getDocument()
    {
        return $this->doc;
    }
}