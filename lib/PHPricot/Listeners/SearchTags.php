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

class PHPricot_Listeners_SearchTags implements PHPricot_Listeners_StartTagListener
{
    private $tags = array();
    private $searchFor = array();

    public function __construct(array $tags)
    {
        $this->searchFor = array_map('strtolower', $tags);
    }

    public function getName()
    {
        return 'searchTags';
    }

    public function startTag(PHPricot_Nodes_Element $element)
    {
        if (in_array($element->name, $this->searchFor)) {
            $this->tags[$element->name][] = $element;
        }
    }

    public function getTags($name)
    {
        $name = strtolower($name);
        if (isset($this->tags[$name])) {
            return $this->tags[$name];
        }
        return array();
    }
}