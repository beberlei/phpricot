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

class PHPricot_Listeners_DebugNodes implements
    PHPricot_Listeners_CommentListener,
    PHPricot_Listeners_EndTagListener,
    PHPricot_Listeners_StartTagListener,
    PHPricot_Listeners_TextListener
{
    public function comment(PHPricot_Nodes_Comment $comment)
    {
        echo "COMMENT[" . substr($comment->data, 0, 100) . "]\n";
    }

    public function endTag(PHPricot_Nodes_Element $element)
    {
        echo "ENDTAG[" . $element->name . "]\n";
    }

    public function getName()
    {
        return 'debug';
    }

    public function startTag(PHPricot_Nodes_Element $element)
    {
        echo "STARTTAG[" . $element->name . "]\n";
    }

    public function text(PHPricot_Nodes_Text $text)
    {
        echo "TEXT[" . substr($text->text, 0, 100) . "]\n";
    }
}