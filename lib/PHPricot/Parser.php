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

class PHPricot_Parser
{
    private $parser;

    private $stack = null;

    /**
     * @var PHPricot_Document
     */
    private $document = null;

    /**
     * @var PHPricot_Nodes_Node
     */
    private $currentParent = null;

    /**
     * @var array
     */
    private $listeners = array();

    /**
     * @var array
     */
    private $events = array(
        'startTag' => array(),
        'endTag' => array(),
        'comment' => array(),
        'text' => array(),
    );

    public function __construct(array $listeners = array())
    {
        if (!extension_loaded('html_parse')) {
            throw new PHPricot_Exception("pecl/html_parse is not installed.");
        }

        foreach ($listeners AS $listener) {
            $this->addListener($listener);
        }
    }

    /**
     * @param PHPricot_Listeners_IListener $listener
     */
    public function addListener(PHPricot_Listeners_IListener $listener)
    {
        $name = $listener->getName();
        $this->listeners[$name] = $listener;
        
        if ($listener instanceof PHPricot_Listeners_StartTagListener) {
            $this->events['startTag'][$name] = $listener;
        }
        if ($listener instanceof PHPricot_Listeners_EndTagListener) {
            $this->events['endTag'][$name] = $listener;
        }
        if ($listener instanceof PHPricot_Listeners_CommentListener) {
            $this->events['comment'][$name] = $listener;
        }
        if ($listener instanceof PHPricot_Listeners_TextListener) {
            $this->events['text'][$name] = $listener;
        }
    }

    /**
     * @param  string $name
     * @return PHPricot_Listeners_IListener
     */
    public function getListener($name)
    {
        if (!isset($this->listeners[$name])) {
            throw new InvalidArgumentException("No listener with name '".$name."' registered with the parser.");
        }
        return $this->listeners[$name];
    }

    /**
     * Parse given html into an AST without attempting to fix any of the HTML
     * 
     * @param  string $html
     * @return PHPricot_Document
     */
    public function parse($html)
    {
        $this->stack = array();
        $this->document = $this->currentParent = new PHPricot_Document();
        $this->parser = html_parser_create();

        html_parser_data_handler($this->parser, array($this, "text"));
        html_parser_starttag_handler($this->parser, array($this, "startTag"));
        html_parser_endtag_handler($this->parser, array($this, "endTag"));
        html_parser_comment_handler($this->parser, array($this, "comment"));
        html_parser_parse($this->parser, $html);
        html_parser_free($this->parser);

        return $this->document;
    }

    public function text($data)
    {
        $textNode = new PHPricot_Nodes_Text($data);
        $this->currentParent->childNodes[] = $textNode;

        if (count($this->events['text'])) {
            foreach ($this->events['text'] AS $listener) {
                $listener->text($textNode);
            }
        }
    }

    public function startTag($tag, $attr)
    {
        $attr = array_reverse($attr, true); // funny bit
        $tag = strtolower($tag);

        $element = new PHPricot_Nodes_Element($tag, $attr);

        if (count($this->events['startTag'])) {
            foreach ($this->events['startTag'] AS $listener) {
                $listener->startTag($element);
            }
        }

        $this->currentParent->childNodes[] = $element;
        
        if (!$element->isSelfClosing()) {
            $this->currentParent = $element;
        
            array_push($this->stack, $element);
        }

        if (isset($attr['id'])) {
            $this->document->idElements[$attr['id']][] = $element;
        }

        if (isset($attr['class'])) {
            foreach (explode(" ", $attr['class']) AS $class) {
                $this->document->classElements[$class][] = $element;
            }
        }
    }

    public function endTag($tag)
    {
        $tag = strtolower($tag);

        if (count($this->stack) == 0) {
            return;
        }

        $stackCopy = $this->stack;
        $currentCopy = $this->currentParent;
        do {
            $this->currentParent = array_pop($this->stack);
            $empty = (count($this->stack) == 0);
            $found = ($this->currentParent && $this->currentParent->name == $tag);
        } while(!$empty && !$found);

        if (!$found) {
            $this->stack = $stackCopy;
            $this->currentParent = $currentCopy;
            return;
        }
        
        $this->currentParent->wasClosed = true;

        if (count($this->events['endTag'])) {
            foreach ($this->events['endTag'] AS $listener) {
                $listener->endTag($this->currentParent);
            }
        }

        if (count($this->stack)) {
            $this->currentParent = end($this->stack);
        } else {
            $this->currentParent = $this->document;
        }
    }

    public function comment($comment)
    {
        $comment = new PHPricot_Nodes_Comment($comment);
        $this->currentParent->childNodes[] = $comment;

        if (count($this->events['comment'])) {
            foreach ($this->events['comment'] AS $listener) {
                $listener->comment($comment);
            }
        }
    }
}