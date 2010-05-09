<?php

if (!function_exists('__autoload')) {
    function __autoload($class)
    {
        require_once __DIR__ . "/../../lib/" . str_replace("_", "/", $class) . ".php";
    }
}

class PHPricot_ParserTest extends PHPUnit_Framework_TestCase
{
    public $p;

    public function setUp()
    {
        $this->p = new PHPricot_Parser();
    }

    public function testBuildReturnsDocument()
    {
        $doc = $this->p->parse('<body><b><a link="foo.html">baz</a> <i>lala</i></body>');
        $this->assertType('PHPricot_Document', $doc);
    }

    static public function dataSimpleHtmlParseRenderEquality()
    {
        return array(
            array('<body><b><a link="foo.html">baz</a> <i>lala</i></body>'),
            array('<b><i><em>Foo</em></i></b>'),
            array('<p class="baz">bar</p>'),
            array('<p></p>'),
            array('Foo'),
            array('<!-- foo -->Foo<strong>Bar</strong>')
        );
    }

    /**
     * @dataProvider dataSimpleHtmlParseRenderEquality
     * @param string $html
     */
    public function testSimpleHtmlParseRenderEquality($html)
    {
        $this->assertEquals($html, $this->p->parse($html)->toHtml());
    }

    public function testBrokenTable()
    {
        $this->assertEquals('<table><tr><td>foo</td>bar</tr></table>', $this->p->parse('<table><tr><td>foo</td>bar</tr></table>')->toHtml());
    }

    public function testBrokenNestedOpenTags()
    {
        $this->assertEquals('<a href="foo"><a href="bar.html">', $this->p->parse('<a href="foo<a href="bar.html">')->toHtml());
    }

    public function testSelfClosingTags_DontHaveChildren()
    {
        $doc = $this->p->parse('<img><img>');

        $this->assertEquals(2, count($doc->childNodes));
        $this->assertEquals('<img />', $doc->childNodes[0]->toHtml());
        $this->assertEquals('<img />', $doc->childNodes[1]->toHtml());
    }

    public function testEkhtmlWebsite()
    {
        $html = file_get_contents(__DIR__ . "/_files/ekhtml.html");
        $doc = $this->p->parse($html);
        $this->assertEquals($html, $doc->toHtml());
    }

    public function testOverClose()
    {
        $this->assertEquals('<div><p></p><p></p></div>', $this->p->parse('<div><p></p></p></p><p></p></div>')->toHtml());
    }

    public function testAttributeOrder()
    {
        $this->assertEquals(
            '<a href="foo.html" class="bar">baz</a><a class="baz" href="foo.html">bar</a>',
            $this->p->parse('<a href="foo.html" class="bar">baz</a><a class="baz" href="foo.html">bar</a>')->toHtml());
    }

    public function testContextNodes()
    {
        $context = new PHPricot_Listeners_CommentContext();
        $search = new PHPricot_Listeners_SearchTags(array('p'));
        $this->p->addListener($context);
        $this->p->addListener($search);

        $doc = $this->p->parse('<!-- BEGIN foo 1 --><p id="foo"><!-- END foo --><p id="bar">');

        $ps = $search->getTags('p');
        $this->assertEquals(2, count($ps));
        foreach ($ps AS $p) {
            if ($p->attributes['id'] == 'foo') {
                $this->assertEquals(array('foo' => 1), $context->getContexts($p));
            } else if ($p->attributes['id'] == 'bar') {
                $this->assertEquals(array(), $context->getContexts($p));
            }
        }
    }
}