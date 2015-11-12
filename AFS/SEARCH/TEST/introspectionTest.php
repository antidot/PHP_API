<?php
/**
 * Created by PhpStorm.
 * User: ct
 * "date": 11/11/15
 * Time: 11:48 AM
 */
require_once('AFS/SEARCH/afs_introspection.php');
require_once('AFS/SEARCH/afs_search.php');
require_once('COMMON/php-SAI/lib/CurlStub.php');
require_once('AIF/afs_user_authentication.php');
require_once('AFS/SEARCH/TEST/DATA/introspection_responses.php');


class IntrospectionTest extends PHPUnit_Framework_TestCase {

  public function testGetFeedListBasics() {
    $introspector = $this->init_introspector(RESPONSE_FACETS_MULTIFEED);
    $expected_feeds_list = array('Categories', 'Catalog');
    $actual_feed_list = $introspector->get_feeds_list();
    $this->assertEquals(sort($expected_feeds_list), sort($actual_feed_list));
  }

  public function testHasFeedBasic() {
    $intropsector = $this->init_introspector(RESULT_WITH_FACETS_FLAT);
    $this->assertTrue($intropsector->has_feed('Catalog'));
    $this->assertFalse($intropsector->has_feed('Categories'));
  }

  public function testGetFeedMetadataShouldReturnNull() {
    $introspector = $this->init_introspector(RESULT_WITHOUT_FACETS);
    $this->assertEquals($introspector->get_feed_metadata('toto'), null);
  }

  public function testGetAllMetadataBasic() {
    $intropsector = $this->init_introspector(RESULT_WITH_FACETS_FLAT);
    $all_metadata = $intropsector->get_all_metadata();
    $this->assertEquals(count($all_metadata), 1);
  }

  public function testIntrospectionWithNoFacets() {
    $intropsector = $this->init_introspector(RESULT_WITHOUT_FACETS);
    $all_meta= $intropsector->get_all_metadata();
    $this->assertEquals(count($all_meta), 0);
  }

  public function testIntrospectionWithFlatFacets() {
    $intropsector = $this->init_introspector(RESULT_WITH_FACETS_FLAT);
    $catalog_metadata= $intropsector->get_feed_metadata('Catalog');
    $catalog_filters_info = $catalog_metadata->get_filters_info();
    $this->assertEquals(count($catalog_filters_info), 3);

    $product= $catalog_filters_info['product_id'];
    $this->check_facet_info($product, 'product_id', false, true, 'INTEGER', 'TREE');

    $name = $catalog_filters_info['name'];
    $this->check_facet_info($name, 'name', false, true, 'STRING', 'TREE');

    $price_from = $catalog_filters_info['price_from'];
    $this->check_facet_info($price_from, 'price_from', false, true, 'REAL', 'TREE');
  }

  public function testIntrospectionWithMultiLevelFacets() {
    $intropsector = $this->init_introspector(RESULT_WITH_FACETS_MULTILEVEL);
    $all_meta= $intropsector->get_all_metadata();
    $this->assertEquals(count($all_meta), 1);
    $catalog_facets_info = $all_meta['Catalog']->get_facets_and_filters_info();
    $this->assertEquals(count($catalog_facets_info), 5);

    $product= $catalog_facets_info['product_id'];
    $this->check_facet_info($product, 'product_id', false, true, 'INTEGER', 'TREE');

    $name = $catalog_facets_info['name'];
    $this->check_facet_info($name, 'name', false, true, 'STRING', 'TREE');

    $price_from = $catalog_facets_info['price_from'];
    $this->check_facet_info($price_from, 'price_from', false, true, 'REAL', 'TREE');

    $product= $catalog_facets_info['product'];
    $this->check_facet_info($product, 'product', false, true, 'STRING', 'TREE');

    $model= $catalog_facets_info['model'];
    $expected_labels = array('FR' => 'Modèle', 'NL' => 'model');
    $this->check_facet_info($model, 'model', false, false, 'STRING', 'TREE', $expected_labels);
  }

  private function check_facet_info($facet_info, $expected_name, $is_sticky, $is_filter, $expected_type,
                                   $expected_layout, $expected_labels=null)
  {
    $this->assertEquals($facet_info->get_id(), $expected_name);
    $this->assertEquals($facet_info->is_sticky(), $is_sticky);
    $this->assertEquals($facet_info->is_filter(), $is_filter);
    $this->assertEquals($facet_info->get_type(), $expected_type);
    $this->assertEquals($facet_info->get_layout(), $expected_layout);
    if (! is_null($expected_labels)) {
      $this->assertEquals($facet_info->get_labels(), $expected_labels);
    }
  }

  private function init_introspector($response)
  {
    $curlConnector = new SAI_CurlStub();
    $mockBaseUrl = "localhost";
    $aboutRequestOpts = array(CURLOPT_URL => "http://$mockBaseUrl/bo-ws/about");
    $aboutResponse = ABOUT_RESPONSE;

    $curlConnector->setResponse($aboutResponse, $aboutRequestOpts);
    $curlConnector->setResponse($response);
    $search = new AfsSearch($mockBaseUrl, '71003', AfsServiceStatus::STABLE, $curlConnector);

    $intropsector = new AfsIntrospection($search);
    return $intropsector;
  }
}

