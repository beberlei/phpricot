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
    public $contexts;
    public $childNodes = array();
    public $wasClosed = false;

    /**
     * @param string $name
     * @param array $attributes
     */
    function __construct($name, array $attributes, array $contexts = array()) {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->contexts = $contexts;
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
    public function html()
    {
        $out = '';
        foreach ($this->childNodes AS $child) {
            $out .= $child->toHtml();
        }
        return $out;
    }

    public function attr($name, $value = null)
    {
        $name = strtolower($name);
        if ($value) {
            $this->attributes[$name] = $value;

            return $this;
        } else if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    public function removeAttr($name)
    {
        $name = strtolower($name);
        unset($this->attributes[$name]);
    }

    public function addClass($name)
    {
        $classes = $this->attr('class');
        if ($classes) {
            $classes = explode(" ", $classes);
        } else {
            $classes = array();
        }
        
        if (!in_array($name, $classes)) {
            $classes[] = $name;
            $this->attr('class', implode(" ", $classes));
        }
        return $this;
    }

    public function removeClass($name)
    {
        $classes = $this->attr('class');
        if ($classes) {
            $classes = array_flip(explode(" ", $classes));
            unset($classes[$name]);
            $this->attr('class', implode(" ", array_flip($classes)));
        }
        return $this;
    }

    public function hasClass($name)
    {
        $classes = $this->attr('class');
        if ($classes) {
            $classes = explode(" ", $classes);
            return array_search($name, $classes) !== false;
        }
        return false;
    }

    public function toggleClass($name)
    {
        if ($this->hasClass($name)) {
            $this->removeClass($name);
        } else {
            $this->addClass($name);
        }
        return $this;
    }

    public function val()
    {
        throw new BadMethodCallException('Not yet implemented');
    }

    public function toText()
    {
        $out = '';

        if ($this->name == 'h1') {
            $out .= "\n" . str_repeat('*', 65) . "\n";
        } else if ($this->name == 'h2') {
            $out .= "\n" . str_repeat('-', 65) . "\n";
        } else if ($this->name == 'li') {
            $out .= " * ";
        }

        foreach ($this->childNodes AS $child) {
            $out .= $child->toText();
        }

        if ($this->name == 'br') {
            $out .= "\n";
        } else if ($this->name == 'p') {
            $out .= "\n\n";
        } else if ($this->name == 'a' && isset($this->attributes['href']) && strpos($this->attributes['href'], "#") !== 0) {
            $out .= "(" . $this->attributes['href'] . ")";
        } else if ($this->name == 'img' && isset($this->attributes['src'])) {
            $out .= "[image:" . $this->attributes['src'] . "]";
        } else if ($this->name == 'h1') {
            $out .= "\n" . str_repeat('*', 65) . "\n\n";
        } else if (in_array($this->name, array('h2', 'h3', 'h4', 'h5', 'h6'))) {
            $out .= "\n" . str_repeat('-', 65) . "\n\n";
        } else if ($this->name == 'td') {
            $out .= "\t";
        } else if ($this->name == 'li') {
            $out .= "\n";
        } else if ($this->name == 'ul') {
            $out .= "\n";
        }

        return $out;
    }

    /**
     * Is this element matching a tag-name, id or classes description?
     *
     * @param string $tagName
     * @param string $id
     * @param string|array $classes
     * @return bool
     */
    public function matching($tagName = null, $id = null, $classes = null, $attrs = null)
    {
        if ($tagName && $tagName != $this->name) {
            return false;
        }
        if ($id && $id !== $this->attr('id')) {
            return false;
        }
        if ($classes) {
            foreach ((array)$classes AS $class) {
                if (!$this->hasClass($class)) {
                    return false;
                }
            }
        }
        if ($attrs) {
            foreach ($attrs AS $name => $value) {
                if ($this->attr($name) != $value) {
                    return false;
                }
            }
        }
        return true;
    }

    public function anyDescendant($tagName, $id, $classes, $attrs)
    {
        $anyDescendants = array();
        foreach ($this->childNodes AS $child) {
            if ($child instanceof PHPricot_Nodes_Element) {
                if ($child->matching($tagName, $id, $classes, $attrs)) {
                    $anyDescendants[] = $child;
                }
                $anyDescendants = array_merge($anyDescendants,
                    $child->anyDescendant($tagName, $id, $classes, $attrs));
            }
        }
        return $anyDescendants;
    }

    public function directDescendant($tagName, $id, $classes, $attrs)
    {
        $directDescendants = array();
        foreach ($this->childNodes AS $child) {
            if ($child instanceof PHPricot_Nodes_Element) {
                if ($child->matching($tagName, $id, $classes, $attrs)) {
                    $directDescendants[] = $child;
                }
            }
        }
        return $directDescendants;
    }
}