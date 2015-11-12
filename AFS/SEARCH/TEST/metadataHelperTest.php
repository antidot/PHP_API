<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 11/13/15
 * Time: 1:47 PM
 */
require_once 'AFS/SEARCH/afs_metadata_helper.php';

class MetadataHelperTest extends PHPUnit_Framework_TestCase {

    private $metadata = null;

    public function setup() {
        $this->metadata = json_decode('
            {
              "uri": "Catalog",
              "meta": {
                "producer": [],
                "info": {
                  "sizeKb": 409093,
                  "date": 1447110000,
                  "searchFeedInfo": {
                    "nbDocs": 33210,
                    "nbShards": 1,
                    "setInfos": [
                      {
                        "setId": "Antidot_Root_Field",
                        "childrenInfos": [],
                        "facetInfos": [
                          {
                            "afs:t": "FacetTree",
                            "layout": "TREE",
                            "type": "INTEGER",
                            "id": "product_id",
                            "sticky": false,
                            "filter": true
                          },
                          {
                            "afs:t": "FacetTree",
                            "layout": "TREE",
                            "type": "STRING",
                            "id": "name",
                            "sticky": false,
                            "filter": true
                          },
                          {
                            "afs:t": "FacetTree",
                            "layout": "TREE",
                            "type": "REAL",
                            "id": "price_from",
                            "sticky": false,
                            "filter": false
                          }
                        ]
                      }
                    ]
                  }
                }
              }
            }
        ');
    }

    public function testFiltersList() {
        $metadata_helper = new AfsMetadataHelper($this->metadata);
        $filters_info = $metadata_helper->get_filters_info();
        $this->assertEquals(count($filters_info), 2);
    }

    public function testFacetslist() {
        $metadata_helper = new AfsMetadataHelper($this->metadata);
        $filters_info = $metadata_helper->get_facets_info();
        $this->assertEquals(count($filters_info), 1);
    }

    public function testFacetsInfoData() {
        $metadata_helper = new AfsMetadataHelper($this->metadata);
        $facets_info = $metadata_helper->get_facets_and_filters_info();
        $this->assertEquals(count($facets_info), 3);

        $this->assertTrue(array_key_exists('product_id', $facets_info));
        $product_id = $facets_info['product_id'];
        $this->assertEquals($product_id->get_id(), 'product_id');
        $this->assertFalse($product_id->is_sticky());
        $this->assertTrue($product_id->is_filter());
        $this->assertEquals($product_id->get_type(), 'INTEGER');
        $this->assertEquals($product_id->get_layout(), 'TREE');

        $this->assertTrue(array_key_exists('name', $facets_info));
        $name = $facets_info['name'];
        $this->assertEquals($name->get_id(), 'name');
        $this->assertFalse($name->is_sticky());
        $this->assertTrue($name->is_filter());
        $this->assertEquals($name->get_type(), 'STRING');
        $this->assertEquals($name->get_layout(), 'TREE');

        $this->assertTrue(array_key_exists('price_from', $facets_info));
        $price_from = $facets_info['price_from'];
        $this->assertEquals($price_from->get_id(), 'price_from');
        $this->assertFalse($price_from->is_sticky());
        $this->assertFalse($price_from->is_filter());
        $this->assertEquals($price_from->get_type(), 'REAL');
        $this->assertEquals($price_from->get_layout(), 'TREE');
    }
}