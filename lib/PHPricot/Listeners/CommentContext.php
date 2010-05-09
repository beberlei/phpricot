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

/**
 * Tags can be in contects which are defined by certain comment sections
 *
 * A context is started by a comment <!-- BEGIN contextName( contextId) --> and
 * ended by <!-- END contextName -->. A context is always unique, if no id is given
 * it is "true"
 *
 */
class PHPricot_Listeners_CommentContext implements PHPricot_Listeners_StartTagListener, PHPricot_Listeners_CommentListener
{
    private $currentContexts = array();

    private $tagContexts = array();

    public function getName()
    {
        return 'commentContext';
    }

    /**
     * @param  PHPricot_Nodes_Element $element
     * @return array
     */
    public function getContexts(PHPricot_Nodes_Element $element)
    {
        $hash = spl_object_hash($element);
        if (isset($this->tagContexts[$hash])) {
            return $this->tagContexts[$hash];
        }
        return array();
    }

    public function comment(PHPricot_Nodes_Comment $comment)
    {
        $parts = explode(" ", trim($comment->data));
        if ($parts[0] == "BEGIN" && isset($parts[1])) {
            $this->currentContexts[$parts[1]] = (isset($parts[2])) ? $parts[2] : true;
        } else if ($parts[0] == "END" && isset($parts[1])) {
            unset($this->currentContexts[$parts[1]]);
        }
    }

    public function startTag(PHPricot_Nodes_Element $element)
    {
        $this->tagContexts[spl_object_hash(($element))] = $this->currentContexts;
    }
}