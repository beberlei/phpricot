<?php

class PHPricot_CssParserTest extends PHPUnit_Framework_TestCase
{
    static public function dataSelector()
    {
        return array(
            array('td', 3, '<table><tr><td>foo</td><td>bar</td><td>baz</td></tr></table>'),
            array('table > td', 0, '<table><tr><td>foo</td><td>bar</td><td>baz</td></tr></table>'),
            array('table > td', 0, '<td></td>'),
            array('tr > td', 3, '<table><tr><td>foo</td><td>bar</td><td>baz</td></tr></table>'),
            array('table > * > td', 3, '<table><tr><td>foo</td><td>bar</td><td>baz</td></tr></table>'),
            array('a.foo', 1, '<a class="foo">bar</a><p class="foo">baz</p>'),
            array('a.foo', 1, '<a class="foo">bar</a><a class="bar">baz</a>'),
            array('a#foo', 1, '<a id="foo">bar</a><a id="baz">bar</a>'),
            array('a[title="baz"]', 1, '<a title="baz"></a><a></a><p title="baz"></p>'),
        );
    }

    static public function dataUnsupportedSelectors()
    {
        return array(
            array('a.foo + .bar'),
            array('td.foo~td.bar'),
            array('a:active'),
            array('a::foo')
        );
    }

    /**
     * @dataProvider dataUnsupportedSelectors
     * @param <type> $selector
     */
    public function testUnsupportedSelectors($selector)
    {
        $cssHandler = new PHPricot_CssParser_EventHandler(new PHPricot_Document);
        $cssParser = new CssParser($selector, $cssHandler);

        $this->setExpectedException('InvalidArgumentException');
        $cssParser->parse();
    }

    /**
     * @dataProvider dataSelector
     * @param <type> $css
     * @param <type> $expectedMatches
     * @param <type> $html
     */
    public function testSelector($css, $expectedMatches, $html)
    {
        $parser  = new PHPricot_Parser();
        $doc = $parser->parse($html);

        $cssHandler = new PHPricot_CssParser_EventHandler($doc);
        $cssParser = new CssParser($css, $cssHandler);
        $cssParser->parse();

        $this->assertEquals($expectedMatches, count($cssHandler->getMatches()));
    }

    public function testWhitewashingLinks()
    {
        $parser  = new PHPricot_Parser();
        $doc = $parser->parse(file_get_contents(dirname(__FILE__) . "/_files/whitewashing.html"));
        
        $cssHandler = new PHPricot_CssParser_EventHandler($doc);
        $cssParser = new CssParser('a', $cssHandler);
        $cssParser->parse();

        $links = $cssHandler->getMatches();

        $this->assertEquals(122, count($links), "Whitewashing Main Page example should have 122 links.");
    }

    public function testWhitewashingHeaders()
    {
        $parser  = new PHPricot_Parser();
        $doc = $parser->parse(file_get_contents(dirname(__FILE__) . "/_files/whitewashing.html"));
        
        $cssHandler = new PHPricot_CssParser_EventHandler($doc);
        $cssParser = new CssParser('div.box h2', $cssHandler);
        $cssParser->parse();

        $headers = $cssHandler->getMatches();

        $this->assertEquals(10, count($headers), "Whitewashing Main Page example should have 10 h2 tags");
    }
}