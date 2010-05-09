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

class PHPricot_Document extends PHPricot_Nodes_Node
{
    public $docType;
    public $childNodes = array();

    public function toHtml()
    {
        if ($this->childNodes) {
            $out = "";
            foreach ($this->childNodes AS $c) {
                $out .= $c->toHtml();
            }

            return $out;
        }
        return '';
    }

    public function toText()
    {
        $txt = '';
        foreach ($this->childNodes AS $node) {
            $txt .= $node->toText();
        }
        $txt = preg_replace('(([\s]{3,}))', '  ', $txt);
        return rtrim($txt);
    }
}