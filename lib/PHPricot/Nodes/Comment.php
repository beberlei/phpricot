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

class PHPricot_Nodes_Comment extends PHPricot_Nodes_Node
{
    public $data;

    public function __construct($comment)
    {
        $this->data = $comment;
    }


    public function toHtml()
    {
        return "<!--" . $this->data . "-->";
    }

    public function toText()
    {

    }
}