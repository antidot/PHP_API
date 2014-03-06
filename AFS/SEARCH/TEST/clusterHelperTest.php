<?php ob_start();

require_once 'AFS/SEARCH/afs_cluster_helper.php';
require_once 'AFS/SEARCH/afs_facet_helper.php';
require_once 'AFS/SEARCH/afs_meta_helper.php';
require_once 'AFS/SEARCH/afs_query.php';
require_once 'AFS/SEARCH/afs_helper_configuration.php';

class ClusterHelperTest extends PHPUnit_Framework_TestCase
{
    public function testCluster()
    {
        $facet_id = 'marketing';
        $facet_label = 'Facet label';
        $value_id = 'OP';
        $value_label = 'Youhou';
        $query = new AfsQuery();

        $input = json_decode('{
            "afs:t": "FacetTree",
            "node": [ {
                    "key": "' . $value_id .'",
                    "labels": [ { "label": "' . $value_label .'" } ],
                    "items": 67
                } ],
            "layout": "TREE",
            "type": "BOOL",
            "id": "' . $facet_id .'",
            "labels": [ { "label": "' . $facet_label .'" } ]
        }');

        $config = new AfsHelperConfiguration();
        $facet_helper = new AfsFacetHelper($input, $query, $config);

        $input = json_decode('{
            "uri": "Catalog",
            "totalItems": 61,
            "totalItemsIsExact": true,
            "pageItems": 20,
            "firstPageItem": 1,
            "lastPageItem": 20,
            "durationMs": 6,
            "cluster": "' . $facet_id . '",
            "firstPaFId": 1,
            "lastPaFId": 1,
            "producer": "SEARCH",
            "totalItemsInClusters": 2,
            "nbClusters": 2
        }');
        $meta = new AfsMetaHelper($input);

        $input = json_decode('{
            "id": "' . $value_id .'",
            "totalItems": 6,
            "totalItemsIsExact": true,
            "pageItems": 1,
            "firstPageItem": 1,
            "lastPageItem": 1,
            "reply": [
                {
                    "docId": 64,
                    "uri": "166_en",
                    "title": [
                        {
                            "afs:t": "KwicString",
                            "text": "HTC Touch Diamond"
                        }
                    ],
                    "relevance": { "rank": 1 }
                }
            ]
        }');

        $helper = new AfsClusterHelper($input, $meta, $facet_helper, $query, $config);
        $this->assertEquals($value_id, $helper->get_id());
        $this->assertEquals($value_label, $helper->get_label());
        $this->assertEquals(6, $helper->get_total_replies());

        $this->assertTrue($helper->has_reply());
        $this->assertEquals(1, $helper->get_nb_replies());

        $query = $helper->get_query();
        $this->assertTrue($query->has_filter($facet_id, $value_id));
    }

    public function testClusterWithoutFacet()
    {
        $facet_id = 'marketing';
        $value_id = 'OP';
        $query = new AfsQuery();

        $input = json_decode('{
            "uri": "Catalog",
            "totalItems": 61,
            "totalItemsIsExact": true,
            "pageItems": 20,
            "firstPageItem": 1,
            "lastPageItem": 20,
            "durationMs": 6,
            "cluster": "marketing",
            "firstPaFId": 1,
            "lastPaFId": 1,
            "producer": "SEARCH",
            "totalItemsInClusters": 2,
            "nbClusters": 2
        }');
        $meta = new AfsMetaHelper($input);

        $input = json_decode('{
            "id": "' . $value_id .'",
            "totalItems": 6,
            "totalItemsIsExact": true,
            "pageItems": 1,
            "firstPageItem": 1,
            "lastPageItem": 1,
            "reply": [
                {
                    "docId": 64,
                    "uri": "166_en",
                    "title": [
                        {
                            "afs:t": "KwicString",
                            "text": "HTC Touch Diamond"
                        }
                    ],
                    "relevance": { "rank": 1 }
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $helper = new AfsClusterHelper($input, $meta, null, $query, $config);
        $this->assertEquals($value_id, $helper->get_id());
        $this->assertEquals($value_id, $helper->get_label());
        $this->assertEquals(6, $helper->get_total_replies());

        $this->assertTrue($helper->has_reply());
        $this->assertEquals(1, $helper->get_nb_replies());
        $replies = $helper->get_replies();
        $this->assertEquals(1, count($replies));
        $reply = $replies[0];
        $this->assertEquals('166_en', $reply->get_uri());

        $query = $helper->get_query();
        $this->assertTrue($query->has_filter($facet_id, $value_id));
    }

    public function testClusterAsArray()
    {
        $facet_id = 'marketing';
        $facet_label = 'Facet label';
        $value_id = 'OP';
        $value_label = 'Youhou';
        $query = new AfsQuery();

        $input = json_decode('{
            "afs:t": "FacetTree",
            "node": [ {
                    "key": "' . $value_id .'",
                    "labels": [ { "label": "' . $value_label .'" } ],
                    "items": 67
                } ],
            "layout": "TREE",
            "type": "BOOL",
            "id": "' . $facet_id .'",
            "labels": [ { "label": "' . $facet_label .'" } ]
        }');

        $config = new AfsHelperConfiguration();
        $facet_helper = new AfsFacetHelper($input, $query, $config);

        $input = json_decode('{
            "uri": "Catalog",
            "totalItems": 61,
            "totalItemsIsExact": true,
            "pageItems": 20,
            "firstPageItem": 1,
            "lastPageItem": 20,
            "durationMs": 6,
            "cluster": "' . $facet_id . '",
            "firstPaFId": 1,
            "lastPaFId": 1,
            "producer": "SEARCH",
            "totalItemsInClusters": 2,
            "nbClusters": 2
        }');
        $meta = new AfsMetaHelper($input);

        $input = json_decode('{
            "id": "' . $value_id .'",
            "totalItems": 6,
            "totalItemsIsExact": true,
            "pageItems": 1,
            "firstPageItem": 1,
            "lastPageItem": 1,
            "reply": [
                {
                    "docId": 64,
                    "uri": "166_en",
                    "title": [
                        {
                            "afs:t": "KwicString",
                            "text": "HTC Touch Diamond"
                        }
                    ],
                    "relevance": { "rank": 1 }
                }
            ]
        }');

        $helper = new AfsClusterHelper($input, $meta, $facet_helper, $query, $config);
        $result = $helper->format();

        $this->assertEquals($value_id, $result['id']);
        $this->assertEquals($value_label, $result['label']);
        $this->assertEquals(6, $result['total_replies']);
        # No query coder provided --> no link
        $this->assertEquals('', $result['link']);

        $replies = $result['replies'];
        $this->assertFalse(empty($replies));
        $this->assertEquals(1, count($replies));
        $this->assertEquals('166_en', $replies[0]['uri']);
    }
}
