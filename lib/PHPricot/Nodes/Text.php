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

class PHPricot_Nodes_Text extends PHPricot_Nodes_Node
{
    public $text;

    public function __construct($text)
    {
        $this->text = $text;
    }


    public function toHtml()
    {
        return $this->text;
    }

    public function toText()
    {
        return $this->text;
    }
}