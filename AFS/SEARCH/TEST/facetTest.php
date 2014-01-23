<?php
require_once "AFS/SEARCH/afs_facet.php";

class FacetTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValues()
    {
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AfsFacetMode::REPLACE);
        $this->assertTrue($facet->has_replace_mode());
        $this->assertFalse($facet->has_add_mode());
        $this->assertTrue($facet->get_combination() == AfsFacetCombination::OR_MODE);
        $this->assertTrue($facet->is_sticky());
    }

    public function testDefaultStickyMode()
    {
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::TREE, AfsFacetMode::ADD, AfsFacetCombination::AND_MODE);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AfsFacetMode::ADD);
        $this->assertFalse($facet->has_replace_mode());
        $this->assertTrue($facet->has_add_mode());
        $this->assertTrue($facet->get_combination() == AfsFacetCombination::AND_MODE);
        $this->assertFalse($facet->is_sticky());
    }

    public function testForceStickyToTrue()
    {
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::TREE, AfsFacetMode::ADD, AfsFacetCombination::AND_MODE, AfsFacetStickyness::STICKY);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AfsFacetMode::ADD);
        $this->assertTrue($facet->get_combination() == AfsFacetCombination::AND_MODE);
        $this->assertTrue($facet->is_sticky());

        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::TREE, AfsFacetMode::ADD, AfsFacetCombination::OR_MODE, AfsFacetStickyness::STICKY);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AfsFacetMode::ADD);
        $this->assertTrue($facet->get_combination() == AfsFacetCombination::OR_MODE);
        $this->assertTrue($facet->is_sticky());
    }
    public function testForceStickyToFalse()
    {
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::TREE, AfsFacetMode::ADD,
            AfsFacetCombination::AND_MODE, AfsFacetStickyness::NON_STICKY);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AfsFacetMode::ADD);
        $this->assertTrue($facet->get_combination() == AfsFacetCombination::AND_MODE);
        $this->assertFalse($facet->is_sticky());

        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::TREE, AfsFacetMode::ADD,
            AfsFacetCombination::OR_MODE, AfsFacetStickyness::NON_STICKY);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AfsFacetMode::ADD);
        $this->assertTrue($facet->get_combination() == AfsFacetCombination::OR_MODE);
        $this->assertFalse($facet->is_sticky());
    }

    public function testFailOnBadModeValue()
    {
        try {
            $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::TREE, 'bar');
            $this->fail('Should have failed on invalid mode value');
        } catch (Exception $e) { }
    }
    public function testFailOnBadCombinationValue()
    {
        try {
            $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::TREE, AfsFacetMode::ADD, 'bar');
            $this->fail('Should have failed on invalid combination value');
        } catch (Exception $e) { }
    }
    public function testFailOnBadStickyValue()
    {
        try {
            $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::TREE, AfsFacetMode::ADD, AfsFacetCombination::AND_MODE, 'bar');
            $this->fail('Should have failed on invalid sticky value');
        } catch (Exception $e) { }
    }

    public function testJoinOneStringValue()
    {
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE);
        $this->assertTrue($facet->join_values(array('bar')) == 'foo="bar"');
    }
    public function testJoinStringValues()
    {
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE);
        $this->assertTrue($facet->join_values(array('bar', 'baz')) == 'foo="bar" or foo="baz"');
    }

    public function testJoinOneValueOtherThanString()
    {
        $facet = new AfsFacet('foo', AfsFacetType::INTEGER_TYPE);
        $this->assertTrue($facet->join_values(array('bar')) == 'foo=bar');
    }
    public function testJoinValuesOtherThanString()
    {
        $facet = new AfsFacet('foo', AfsFacetType::INTEGER_TYPE, AfsFacetLayout::TREE, AfsFacetMode::ADD, AfsFacetCombination::AND_MODE);
        $this->assertTrue($facet->join_values(array('bar', 'baz')) == 'foo=bar and foo=baz');
    }
}
?>
