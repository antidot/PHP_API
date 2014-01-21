<?php
require_once "COMMON/afs_tools.php";

class ToolsTest extends PHPUnit_Framework_TestCase
{
    public function testReplaceFirstOccurrence()
    {
        $this->assertEquals(str_replace_first('f', 'b', 'foo'), 'boo');
        $this->assertEquals(str_replace_first('f', 'b', 'boofoo'), 'booboo');
        $this->assertEquals(str_replace_first('foo', 'bar', 'boofoo'), 'boobar');
    }

    public function testReplaceFirstOccurrenceNotFound()
    {
        $this->assertEquals(str_replace_first('f', 'b', 'zoo'), 'zoo');
        $this->assertEquals(str_replace_first('fo', 'ba', 'zoo'), 'zoo');
    }

    public function testGetTextFromDOMNode()
    {
        $doc = new DOMDocument();
        $doc->loadXML('<r>foo</r>');
        $this->assertEquals(DOMNodeHelper::get_text($doc->documentElement), 'foo');
    }

    public function testGetTextFromDOMNodeWithChild()
    {
        $doc = new DOMDocument();
        $doc->loadXML('<r>foo <u>bar</u> baz</r>');
        $this->assertEquals(DOMNodeHelper::get_text($doc->documentElement), 'foo  baz');
    }

    public function testGetTextFromDOMNodeAndChildren()
    {
        $doc = new DOMDocument();
        $doc->loadXML('<r>foo <u>bar</u> <u>baz</u> zoo <a>arf</a> final</r>');
        $this->assertEquals(DOMNodeHelper::get_text($doc->documentElement, array(XML_ELEMENT_NODE => 'DOMNodeHelper::get_text')), 'foo bar baz zoo arf final');
    }

    public function testGetTextFromDOMNodeAndSpecificChildren()
    {
        $doc = new DOMDocument();
        $doc->loadXML('<r>foo <u>bar</u> <u>baz</u> zoo <a>arf</a> final</r>');
        $filter = new FilterNode('u');
        $this->assertEquals(DOMNodeHelper::get_text($doc->documentElement, array(XML_ELEMENT_NODE => $filter)), 'foo bar baz zoo  final');
    }

    public function testGetTextFromDOMNodeAndSpecificChildrenWithSpecificFormat()
    {
        $doc = new DOMDocument();
        $doc->loadXML('<r>foo <u>bar</u> <u>baz</u> zoo <a>arf</a> final</r>');
        $filter = new BoldFilterNode('u');
        $this->assertEquals(DOMNodeHelper::get_text($doc->documentElement, array(XML_ELEMENT_NODE => $filter)), 'foo <b>bar</b> <b>baz</b> zoo  final');
    }
}

?>
