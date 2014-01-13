<?php
require_once "afs_connector_interface.php";
require_once "afs_search_query_manager.php";
require_once "afs_query.php";

class ConnectorMock implements AfsConnectorInterface
{
    private $parameters = null;

    public function send(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function get_parameters()
    {
        return $this->parameters;
    }
}


class SearchQueryManagerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->connector = new ConnectorMock();
        $this->facet_mgr = new AfsFacetManager();
        $this->qm = new AfsSearchQueryManager($this->connector, $this->facet_mgr);
    }

    private function checkFacetOrder(array $values)
    {
        $params = $this->connector->get_parameters();
        $facet_order = $params['afs:facetOrder'];
        if (empty($values))
        {
            // assertEmpty not available in this PHPUnitTest version!
            $this->assertTrue(empty($facet_order));
        }

        $facet_order = explode(',', $facet_order);
        foreach ($values as $key => $value)
        {
            $this->assertTrue($facet_order[$key] == $value);
        }
    }

    private function checkOneFacetValue($facet_id, $facet_value)
    {
        $params = $this->connector->get_parameters();
        $filter_str = explode('=', $params['afs:filter'][0], 2);
        $filter[$filter_str[0]] = $filter_str[1];
        $facet = $filter[$facet_id];
        $this->assertTrue($facet == $facet_value);
    }

    private function checkFacetValues($facet_id, $facet_values, $split)
    {
        $params = $this->connector->get_parameters();
        $filters = explode(' ' . $split . ' ', $params['afs:filter'][0]);
        $facets = array();
        foreach ($filters as $filter)
        {
            $facet_str = explode('=', $filter, 2);
            if (empty($facets[$facet_str[0]]))
            {
                $facets[$facet_str[0]] = array();
            }
            $facets[$facet_str[0]][] = $facet_str[1];
        }

        foreach ($facet_values as $value)
        {
            $this->assertTrue(in_array($value, $facets[$facet_id]));
        }
    }

    private function checkFromValue($origin)
    {
        $params = $this->connector->get_parameters();
        $this->assertTrue(array_key_exists('afs:from', $params));
        $this->assertEquals($origin, $params['afs:from']);
    }

    public function testNoParameterProvided()
    {
        $query = new AfsQuery();
        $this->qm->send($query);
        $this->checkFacetOrder(array());
    }

    public function testUnregisteredFacet()
    {
        try
        {
            $query = new AfsQuery();
            $query = $query->add_filter('foo', 'bar');
            $this->qm->send($query);
            $this->fail('No error whereas provided facet hsa not been configured!');
        }
        catch (Exception $e)
        { }
    }

    public function testOneFacetOneValue()
    {
        $facet = new AfsFacet('foo', AFS_FACET_STRING);
        $this->facet_mgr->add_facet($facet);

        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $this->qm->send($query);
        $this->checkFacetOrder(array('foo'));
        $this->checkOneFacetValue('foo', '"bar"');
    }

    public function testFailOneFacetOneValue()
    {
        $facet = new AfsFacet('foo', AFS_FACET_INTEGER);
        $this->facet_mgr->add_facet($facet);

        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $this->qm->send($query);
        $this->checkFacetOrder(array('foo'));
        try
        {
            $this->checkOneFacetValue('foo', '"bar"');
            $this->fail('Should have failed due to value type/reference provided!');
        }
        catch (Exception $e)
        { }
    }

    public function testOneFacetMultipleValues()
    {
        $facet = new AfsFacet('foo', AFS_FACET_INTEGER, AFS_FACET_ADD, AFS_FACET_OR);
        $this->facet_mgr->add_facet($facet);

        $query = new AfsQuery();
        $query = $query->add_filter('foo', '4');
        $query = $query->add_filter('foo', '2');
        $this->qm->send($query);
        $this->checkFacetOrder(array('foo'));
        $this->checkFacetValues('foo', array('4', '2'), 'or');
    }

    public function testFailOnValueOneFacetMultipleValues()
    {
        $facet = new AfsFacet('foo', AFS_FACET_INTEGER, AFS_FACET_ADD, AFS_FACET_OR);
        $this->facet_mgr->add_facet($facet);

        $query = new AfsQuery();
        $query = $query->add_filter('foo', '4');
        $query = $query->add_filter('foo', '2');
        $this->qm->send($query);
        $this->checkFacetOrder(array('foo'));
        try
        {
            $this->checkFacetValues('foo', array('4', '3'), 'or');
            $this->fail('Should have failed due to invalid value provided!');
        }
        catch (Exception $e)
        { }
    }

    public function testFailOnModeValueOneFacetMultipleValues()
    {
        $facet = new AfsFacet('foo', AFS_FACET_INTEGER, AFS_FACET_ADD, AFS_FACET_OR);
        $this->facet_mgr->add_facet($facet);

        $query = new AfsQuery();
        $query = $query->add_filter('foo', '4');
        $query = $query->add_filter('foo', '2');
        $this->qm->send($query);
        $this->checkFacetOrder(array('foo'));
        try
        {
            $this->checkFacetValues('foo', array('4', '2'), 'and');
            $this->fail('Should have failed due to invalid mode provided!');
        }
        catch (Exception $e)
        { }
    }

    public function testFromParameter()
    {
        $facet = new AfsFacet('foo', AFS_FACET_INTEGER, AFS_FACET_ADD, AFS_FACET_OR);
        $this->facet_mgr->add_facet($facet);

        $query = new AfsQuery();
        $query = $query->add_filter('foo', '4');
        $query = $query->add_filter('foo', '2');
        $this->qm->send($query);
        $this->checkFromValue(AfsOrigin::FACET);
    }
}
