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

require_once dirname(__FILE__) . "/../../vendor/CssParser.php";

class PHPricot_CssParser_EventHandler implements CssEventHandler
{
    const OP_ANY = 1;
    const OP_DIRECT = 2;

    private $commandStack = array(
        array('operator' => self::OP_ANY, 'tag' => null, 'id' => null, 'classes' => array(), 'attrs' => array())
    );

    /**
     * @var PHPricot_Document
     */
    private $doc = null;

    public function __construct(PHPricot_Document $doc)
    {
        $this->doc = $doc;
    }

    public function adjacent() {
        // do nothing?
    }

    public function anotherSelector()
    {
        throw new InvalidArgumentException('PHPricot currently does not support CSS3 anotherSelector tokens.');
    }
    
    public function anyDescendant()
    {
        $this->commandStack[] = array(
            'operator' => self::OP_ANY, 'tag' => null, 'id' => null, 'classes' => array(), 'attrs' => array()
        );
    }

    public function anyElement()
    {
        $num = count($this->commandStack) - 1;
        $this->commandStack[$num]['tag'] = null;
    }

    public function attribute($name, $value = NULL, $operation = CssEventHandler::isExactly)
    {
        $num = count($this->commandStack) - 1;
        $this->commandStack[$num]['attrs'][$name] = $value;
    }

    public function directDescendant()
    {
        $this->commandStack[] = array(
            'operator' => self::OP_DIRECT, 'tag' => null, 'id' => null, 'classes' => array(), 'attrs' => array()
        );
    }

    public function element($name)
    {
        $num = count($this->commandStack) - 1;
        $this->commandStack[$num]['tag'] = $name;
    }
    
    public function elementClass($name)
    {
        $num = count($this->commandStack) - 1;
        $this->commandStack[$num]['classes'][] = $name;
    }

    public function elementID($id)
    {
        $num = count($this->commandStack) - 1;
        $this->commandStack[$num]['id'] = $id;
    }
    
    public function elementNS($name, $namespace = NULL)
    {
        throw new InvalidArgumentException('PHPricot currently does not support CSS3 elementNS tokens.');
    }
    
    public function pseudoClass($name, $value = NULL)
    {
        throw new InvalidArgumentException('PHPricot currently does not support CSS3 pseudoClass tokens.');
    }
    public function pseudoElement($name)
    {
        throw new InvalidArgumentException('PHPricot currently does not support CSS3 pseudoElement tokens.');
    }

    public function sibling()
    {
        throw new InvalidArgumentException('PHPricot currently does not support CSS3 sibling tokens.');
    }

    public function attributeNS($name, $ns, $value = NULL, $operation = CssEventHandler::isExactly)
    {
        throw new InvalidArgumentException('PHPricot currently does not support CSS3 attributeNS tokens.');
    }

    public function anyElementInNS($ns)
    {
        throw new InvalidArgumentException('PHPricot currently does not support CSS3 anyElementInNS tokens.');
    }

    public function getMatches()
    {
        $currentMatches = $this->doc->childNodes;

        do {
            $cmd = array_shift($this->commandStack);
            $currentResults = array();
            foreach ($currentMatches AS $match) {
                if ($match instanceof PHPricot_Nodes_Element) {
                    if ($cmd['operator'] == self::OP_ANY) {
                        if ($match->matching($cmd['tag'], $cmd['id'], $cmd['classes'], $cmd['attrs'])) {
                            $currentResults[] = $match;
                        }

                        $currentResults = array_merge(
                            $currentResults, $match->anyDescendant($cmd['tag'], $cmd['id'], $cmd['classes'], $cmd['attrs'])
                        );
                    } else if($cmd['operator'] == self::OP_DIRECT) {
                        if ($match->matching($cmd['tag'], $cmd['id'], $cmd['classes'], $cmd['attrs'])) {
                            $currentResults[] = $match;
                        }

                        $currentResults = array_merge(
                            $currentResults, $match->directDescendant($cmd['tag'], $cmd['id'], $cmd['classes'], $cmd['attrs'])
                        );
                    }
                }
            }
            $currentMatches = $currentResults;
        } while(count($this->commandStack));

        return $currentResults;
    }
}