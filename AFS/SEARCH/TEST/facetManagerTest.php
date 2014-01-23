<?php
require_once "AFS/SEARCH/afs_facet_manager.php";

class FacetManagerTest extends PHPUnit_Framework_TestCase
{
    public function testNoFacetDefinedGetEmptyFacetList()
    {
        $mgr = new AfsFacetManager();
        $this->assertEquals(count($mgr->get_facets()), 0);
    }

    public function testNoFacetDefinedFailOnGetSpecificFacetName()
    {
        $mgr = new AfsFacetManager();
        try {
            $mgr->get_facet('foo');
            $this->fail('Query of unknown facet should have rosen an exception!');
        } catch (OutOfBoundsException $e) {}
    }

    public function testGetDefinedFacet()
    {
        $mgr = new AfsFacetManager();
        $mgr->add_facet(new AfsFacet('foo', AfsFacetType::STRING_TYPE));
        $this->assertEquals($mgr->get_facet('foo')->get_id(), 'foo');
    }

    public function testGetAllFacets()
    {
        $mgr = new AfsFacetManager();
        $mgr->add_facet(new AfsFacet('foo', AfsFacetType::STRING_TYPE));
        $this->assertTrue(array_key_exists('foo', $mgr->get_facets()));
    }

    public function testAddFacetWithSameName()
    {
        $mgr = new AfsFacetManager();
        $mgr->add_facet(new AfsFacet('foo', AfsFacetType::STRING_TYPE));
        try {
            $mgr->add_facet(new AfsFacet('foo', AfsFacetType::DATE_TYPE));
        } catch (InvalidArgumentException $e) { }
    }
}

?>
