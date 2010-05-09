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

interface PHPricot_Listeners_StartTagListener extends PHPricot_Listeners_IListener
{
    /**
     * @param PHPricot_Nodes_Element $element
     */
    public function startTag(PHPricot_Nodes_Element $element);
}