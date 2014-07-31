<?php ob_start();
require_once "AFS/SEARCH/afs_facet.php";

class FacetTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValues()
    {
        $facet = new AfsFacet('foo');
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertEquals(AfsFacetType::UNKNOWN_TYPE, $facet->get_type());
        $this->assertEquals(AfsFacetLayout::TREE, $facet->get_layout());
        $this->assertEquals(AfsFacetMode::UNSPECIFIED_MODE, $facet->get_mode());
        $this->assertFalse($facet->has_or_mode());
        $this->assertFalse($facet->has_and_mode());
        $this->assertFalse($facet->has_single_mode());
    }

    public function testFailOnBadIdentifier()
    {
        try {
            $facet = new AfsFacet(NULL);
            $this->fail('Should have failed on invalid identifier');
        } catch (InvalidArgumentException $e) { }
        try {
            $facet = new AfsFacet('');
            $this->fail('Should have failed on invalid identifier');
        } catch (InvalidArgumentException $e) { }
        try {
            $facet = new AfsFacet('4foo');
            $this->fail('Should have failed on invalid identifier');
        } catch (InvalidArgumentException $e) { }
        try {
            $facet = new AfsFacet('foo bar');
            $this->fail('Should have failed on invalid identifier');
        } catch (InvalidArgumentException $e) { }

    }

    public function testFailOnBadTypeValue()
    {
        try {
            $facet = new AfsFacet('foo', 'foo');
            $this->fail('Should have failed on invalid type value');
        } catch (Exception $e) { }
    }
    public function testFailOnBadLayoutValue()
    {
        try {
            $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, 'foo');
            $this->fail('Should have failed on invalid sticky value');
        } catch (Exception $e) { }
    }
    public function testFailOnBadModeValue()
    {
        try {
            $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::TREE, 'bar');
            $this->fail('Should have failed on invalid mode value');
        } catch (Exception $e) { }
    }

    public function testJoinOneStringValue()
    {
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE);
        $this->assertEquals('foo="bar"', $facet->join_values(array('"bar"')));
    }
    public function testJoinStringValues()
    {
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::TREE, AfsFacetMode::OR_MODE);
        $this->assertEquals('foo="bar" or foo="baz"', $facet->join_values(array('"bar"', '"baz"')));
    }

    public function testJoinOneValueOtherThanString()
    {
        $facet = new AfsFacet('foo', AfsFacetType::INTEGER_TYPE);
        $this->assertEquals('foo=bar', $facet->join_values(array('bar')));
    }
    public function testJoinValuesOtherThanString()
    {
        $facet = new AfsFacet('foo', AfsFacetType::INTEGER_TYPE, AfsFacetLayout::TREE, AfsFacetMode::AND_MODE);
        $this->assertEquals('foo=bar and foo=baz', $facet->join_values(array('bar', 'baz')));
    }

    public function testFacetAreSimilar()
    {
        $facet = new AfsFacet('foo', AfsFacetType::BOOL_TYPE);
        $other = new AfsFacet('foo', AfsFacetType::BOOL_TYPE, AfsFacetLayout::TREE);
        $this->assertTrue($facet->is_similar_to($facet));
        $this->assertTrue($facet->is_similar_to($other));
    }
    public function testFacetOfDifferentName()
    {
        $facet = new AfsFacet('foo', AfsFacetType::BOOL_TYPE);
        $other = new AfsFacet('FOO', AfsFacetType::BOOL_TYPE, AfsFacetLayout::TREE);
        $this->assertFalse($facet->is_similar_to($other));
    }
    public function testFacetOfDifferentType()
    {
        $facet = new AfsFacet('foo', AfsFacetType::BOOL_TYPE);
        $other = new AfsFacet('foo', AfsFacetType::DATE_TYPE, AfsFacetLayout::TREE);
        $this->assertFalse($facet->is_similar_to($other));
    }
    public function testFacetOfDifferentLayout()
    {
        $facet = new AfsFacet('foo', AfsFacetType::BOOL_TYPE);
        $other = new AfsFacet('foo', AfsFacetType::BOOL_TYPE, AfsFacetLayout::INTERVAL);
        $this->assertFalse($facet->is_similar_to($other));
    }
}

