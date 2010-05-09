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

class PHPricot_Nodes_Element extends PHPricot_Nodes_Node
{
    static private $selfClosing = array('base', 'meta', 'link', 'hr', 'br', 'param', 'img', 'area', 'input', 'col');

    public $name;
    public $attributes;
    public $childNodes = array();
    public $wasClosed = false;

    /**
     * @param string $name
     * @param array $attributes
     */
    function __construct($name, array $attributes) {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    public function isSelfClosing()
    {
        return in_array($this->name, self::$selfClosing);
    }

    public function toHtml()
    {
        $out = '<' . $this->name;
        foreach ($this->attributes AS $k => $v) {
            $out .= ' ' . $k . '="' . $v . '"';
        }
        if ($this->isSelfClosing()) {
            $out .= ' />';
        } else {
            $out .= '>';
            foreach ($this->childNodes AS $child) {
                $out .= $child->toHtml();
            }
            if ($this->wasClosed) {
                $out .= '</' . $this->name . '>';
            }
        }
        return $out;
    }

    /**
     * @return string
     */
    public function innerHtml()
    {
        $out = '';
        foreach ($this->childNodes AS $child) {
            $out .= $child->toHtml();
        }
        return $out;
    }

    public function toText()
    {
        $out = '';
        foreach ($this->childNodes AS $child) {
            $out .= $child->toText();
        }

        if ($this->name == 'br') {
            $out .= "\n";
        } else if ($this->name == 'p') {
            $out .= "\n\n";
        } else if ($this->name == 'a' && isset($this->attributes['href'])) {
            $out .= "[#" . $this->attributes['href'] . "]";
        } else if ($this->name == 'img' && isset($this->attributes['src'])) {
            $out .= "[image:" . $this->attributes['src'] . "]";
        }

        return $out;
    }
}