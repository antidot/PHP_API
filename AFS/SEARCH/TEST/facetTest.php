<?php
require_once "AFS/SEARCH/afs_facet.php";

class FacetTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultValues()
    {
        $facet = new AfsFacet('foo', AFS_FACET_STRING);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AFS_FACET_REPLACE);
        $this->assertTrue($facet->has_replace_mode());
        $this->assertFalse($facet->has_add_mode());
        $this->assertTrue($facet->get_combination() == AFS_FACET_OR);
        $this->assertTrue($facet->is_sticky());
    }

    public function testDefaultStickyMode()
    {
        $facet = new AfsFacet('foo', AFS_FACET_STRING, AFS_FACET_ADD, AFS_FACET_AND);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AFS_FACET_ADD);
        $this->assertFalse($facet->has_replace_mode());
        $this->assertTrue($facet->has_add_mode());
        $this->assertTrue($facet->get_combination() == AFS_FACET_AND);
        $this->assertFalse($facet->is_sticky());
    }

    public function testForceStickyToTrue()
    {
        $facet = new AfsFacet('foo', AFS_FACET_STRING, AFS_FACET_ADD, AFS_FACET_AND, true);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AFS_FACET_ADD);
        $this->assertTrue($facet->get_combination() == AFS_FACET_AND);
        $this->assertTrue($facet->is_sticky());

        $facet = new AfsFacet('foo', AFS_FACET_STRING, AFS_FACET_ADD, AFS_FACET_OR, true);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AFS_FACET_ADD);
        $this->assertTrue($facet->get_combination() == AFS_FACET_OR);
        $this->assertTrue($facet->is_sticky());
    }
    public function testForceStickyToFalse()
    {
        $facet = new AfsFacet('foo', AFS_FACET_STRING, AFS_FACET_ADD,
            AFS_FACET_AND, AFS_FACET_NON_STICKY);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AFS_FACET_ADD);
        $this->assertTrue($facet->get_combination() == AFS_FACET_AND);
        $this->assertFalse($facet->is_sticky());

        $facet = new AfsFacet('foo', AFS_FACET_STRING, AFS_FACET_ADD,
            AFS_FACET_OR, AFS_FACET_NON_STICKY);
        $this->assertTrue($facet->get_id() == 'foo');
        $this->assertTrue($facet->get_mode() == AFS_FACET_ADD);
        $this->assertTrue($facet->get_combination() == AFS_FACET_OR);
        $this->assertFalse($facet->is_sticky());
    }

    public function testFailOnBadModeValue()
    {
        try {
            $facet = new AfsFacet('foo', AFS_FACET_STRING, 'bar');
            $this->fail('Should have failed on invalid mode value');
        } catch (Exception $e) { }
    }
    public function testFailOnBadCombinationValue()
    {
        try {
            $facet = new AfsFacet('foo', AFS_FACET_STRING, AFS_FACET_ADD, 'bar');
            $this->fail('Should have failed on invalid combination value');
        } catch (Exception $e) { }
    }
    public function testFailOnBadStickyValue()
    {
        try {
            $facet = new AfsFacet('foo', AFS_FACET_STRING, AFS_FACET_ADD, AFS_FACET_AND, 'bar');
            $this->fail('Should have failed on invalid sticky value');
        } catch (Exception $e) { }
    }

    public function testJoinOneStringValue()
    {
        $facet = new AfsFacet('foo', AFS_FACET_STRING);
        $this->assertTrue($facet->join_values(array('bar')) == 'foo="bar"');
    }
    public function testJoinStringValues()
    {
        $facet = new AfsFacet('foo', AFS_FACET_STRING);
        $this->assertTrue($facet->join_values(array('bar', 'baz')) == 'foo="bar" or foo="baz"');
    }

    public function testJoinOneValueOtherThanString()
    {
        $facet = new AfsFacet('foo', AFS_FACET_INTEGER);
        $this->assertTrue($facet->join_values(array('bar')) == 'foo=bar');
    }
    public function testJoinValuesOtherThanString()
    {
        $facet = new AfsFacet('foo', AFS_FACET_INTEGER, AFS_FACET_ADD, AFS_FACET_AND);
        $this->assertTrue($facet->join_values(array('bar', 'baz')) == 'foo=bar and foo=baz');
    }
}
?>
