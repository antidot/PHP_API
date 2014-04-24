<?php ob_start();
require_once "COMMON/afs_tools.php";


class MyEnum extends BasicEnum
{
    private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

    const FOO = 'foo';
    const BAR = 'bar';
}


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

    public function testGetTextFromDOMNodeWithMultipleCallbacks()
    {
        $doc = new DOMDocument();
        $doc->loadXML('<r>foo <u>bar</u> <u>baz</u> zoo <a>arf</a> final</r>');
        $filter1 = new BoldFilterNode('u');
        $filter2 = new FilterNode('a');
        $this->assertEquals('foo <b>bar</b> <b>baz</b> zoo arf final',
            DOMNodeHelper::get_text($doc->documentElement, array(XML_ELEMENT_NODE => array($filter1, $filter2))));
    }

    public function testInvalidEnumValue()
    {
        try {
            MyEnum::check_value('FOO');
            $this->fail();
        } catch (InvalidArgumentException $e) {
        }
        try {
            MyEnum::check_value('Foo');
            $this->fail();
        } catch (InvalidArgumentException $e) {
        }
    }

    public function testCheckValidValue()
    {
        try
        {
            MyEnum::check_value(MyEnum::FOO);
            MyEnum::check_value('foo');
            MyEnum::check_value(MyEnum::BAR);
            MyEnum::check_value('bar');
        } catch (Exception $e) {
            $this->fail('Should not have raise any exception!');
        }
    }

    public function testCheckInvalidValue()
    {
        try
        {
            MyEnum::check_value('Foo');
            $this->fail('Invalid checked value should have raised an exception!');
        } catch (InvalidArgumentException $e) { }
    }
}


