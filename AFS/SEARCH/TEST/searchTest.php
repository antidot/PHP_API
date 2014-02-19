<?php ob_start();
require_once 'AFS/SEARCH/afs_search.php';

class SearchTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveDefaultParameters()
    {
        $search = new AfsSearch('127.0.0.1', 666);

        $service = $search->get_service();
        $this->assertEquals(666, $service->id);
        $this->assertEquals(AfsServiceStatus::STABLE, $service->status);

        $search->execute();
        $url = $search->get_generated_url();
        $this->assertTrue(strpos($url, '127.0.0.1') !== False, 'URL does not contain right host');
        $this->assertTrue(strpos($url, 'service=666') !== False, 'URL does not contain right sesrvice id');
        $this->assertTrue(strpos($url, 'status=stable') !== False, 'URL does not contain right sesrvice status');

        $config = $search->get_helpers_configuration();
        $this->assertEquals(AfsHelperFormat::ARRAYS, $config->get_helper_format());
    }

    public function testRetrieveSpecificParameters()
    {
        $search = new AfsSearch('127.0.0.2', 42, AfsServiceStatus::RC);

        $service = $search->get_service();
        $this->assertEquals(42, $service->id);
        $this->assertEquals(AfsServiceStatus::RC, $service->status);

        $search->execute(AfsHelperFormat::HELPERS);
        $url = $search->get_generated_url();
        $this->assertTrue(strpos($url, '127.0.0.2') !== False, 'URL does not contain right host');
        $this->assertTrue(strpos($url, 'service=42') !== False, 'URL does not contain right sesrvice id');
        $this->assertTrue(strpos($url, 'status=rc') !== False, 'URL does not contain right sesrvice status');

        $config = $search->get_helpers_configuration();
        $this->assertEquals(AfsHelperFormat::HELPERS, $config->get_helper_format());
    }

    public function testSetQuery()
    {
        $search = new AfsSearch('127.0.0.1', 666);
        $query = new AfsQuery();
        $query = $query->set_query('foo');
        $search->set_query($query);

        $this->assertEquals('foo', $search->get_query()->get_query());

        $search->execute();
        $this->assertTrue(strpos($search->get_generated_url(), 'query=foo') !== False, 'URL does not contain query!');
    }

    public function testAddFacet()
    {
        $search = new AfsSearch('127.0.0.1', 42);
        $facet_mgr = $search->get_helpers_configuration()->get_facet_manager();
        $this->assertFalse($facet_mgr->has_facet('FOO'));

        $search->add_facet(new AfsFacet('FOO', AfsFacetType::STRING_TYPE, AfsFacetLayout::INTERVAL));
        $this->assertTrue($facet_mgr->has_facet('FOO'));
    }

    public function testDefaultFacetOptionNonSticky()
    {
        $search = new AfsSearch('127.0.0.1', 42);
        $search->set_facets_stickyness(false);
        $facet_mgr = $search->get_helpers_configuration()->get_facet_manager();
        $this->assertFalse($facet_mgr->get_facets_stickyness());
    }
    public function testDefaultFacetOptionSticky()
    {
        $search = new AfsSearch('127.0.0.1', 42);
        $search->set_facets_stickyness(true);
        $facet_mgr = $search->get_helpers_configuration()->get_facet_manager();
        $this->assertTrue($facet_mgr->get_facets_stickyness());
    }
    public function testFacetNonSticky()
    {
        $search = new AfsSearch('127.0.0.1', 42);
        $search->set_facet_stickyness('FOO', false);
        $facet_mgr = $search->get_helpers_configuration()->get_facet_manager();
        $this->assertTrue($facet_mgr->has_facet('FOO'));
        $facet = $facet_mgr->get_facet('FOO');
        $this->assertFalse($facet->is_sticky());
    }
    public function testFacetSticky()
    {
        $search = new AfsSearch('127.0.0.1', 42);
        $search->set_facet_stickyness('FOO', true);
        $facet_mgr = $search->get_helpers_configuration()->get_facet_manager();
        $this->assertTrue($facet_mgr->has_facet('FOO'));
        $facet = $facet_mgr->get_facet('FOO');
        $this->assertTrue($facet->is_sticky());
    }

    public function testSmoothFacetSortOrder()
    {
        $search = new AfsSearch('127.0.0.1', 42);
        $this->assertFalse($search->get_helpers_configuration()->get_facet_manager()->is_facet_sort_order_strict());
    }
    public function testStrictFacetSortOrder()
    {
        $search = new AfsSearch('127.0.0.1', 42);
        $search->set_facet_sort_order(array('foo', 'bar'), AfsFacetSort::STRICT);
        $this->assertTrue($search->get_helpers_configuration()->get_facet_manager()->is_facet_sort_order_strict());
    }
}
