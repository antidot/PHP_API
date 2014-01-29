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

    public function testHasDefinedFacet()
    {
        $mgr = new AfsFacetManager();
        $mgr->add_facet(new AfsFacet('foo', AfsFacetType::STRING_TYPE));
        $this->assertTrue($mgr->has_facet('foo'));
    }
    public function testHasNotFacet()
    {
        $mgr = new AfsFacetManager();
        $mgr->add_facet(new AfsFacet('foo', AfsFacetType::STRING_TYPE));
        $this->assertFalse($mgr->has_facet('bar'));
    }

    public function testCheckExistingFacet()
    {
        $mgr = new AfsFacetManager();
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE);
        $mgr->add_facet($facet);
        try {
            $mgr->check_facet($facet);
        } catch (Exception $e) {
            $this->fail('Check of existing facet with right parameters should not have raised any exception! '
                . $e);
        }
    }
    public function testCheckUnexistingFacet()
    {
        $mgr = new AfsFacetManager();
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE);
        try {
            $mgr->check_facet($facet);
            $this->fail('Check of unknown facet should have raise exception');
        } catch (AfsUndefinedFacetException $e) { }
    }
    public function testCheckFacetWithImproperParameters()
    {
        $mgr = new AfsFacetManager();
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE);
        $mgr->add_facet(new AfsFacet('foo', AfsFacetType::INTEGER_TYPE));
        try {
            $mgr->check_facet($facet);
            $this->fail('Check of invalid facet parameters should have raise exception');
        } catch (AfsInvalidFacetParameterException $e) { }
    }

    public function testCheckOrAddNewFacet()
    {
        $mgr = new AfsFacetManager();
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE);
        try {
            $mgr->check_or_add_facet($facet);
        } catch (Exception $e) {
            $this->fail('New facet should have been added!');
        }
        $this->assertTrue($mgr->has_facet('foo'));
    }
    public function testCheckOrAddExistingFacet()
    {
        $mgr = new AfsFacetManager();
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE);
        $mgr->add_facet($facet);
        try {
            $mgr->check_or_add_facet($facet);
        } catch (Exception $e) {
            $this->fail('New facet should have been added!');
        }
        $this->assertTrue($mgr->has_facet('foo'));
    }
    public function testCheckOrAddExistingFacetWithDifferentParameter()
    {
        $mgr = new AfsFacetManager();
        $facet = new AfsFacet('foo', AfsFacetType::STRING_TYPE);
        $mgr->add_facet(new AfsFacet('foo', AfsFacetType::STRING_TYPE, AfsFacetLayout::INTERVAL));
        try {
            $mgr->check_or_add_facet($facet);
            $this->fail('Existing facet with different parameters should have raise exception!');
        } catch (AfsInvalidFacetParameterException $e) { }
    }
}


