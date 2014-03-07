<?php ob_start();
require_once "AFS/SEARCH/afs_meta_helper.php";

class MetaHelperTest extends PHPUnit_Framework_TestCase
{
    public function testValues()
    {
        $input = json_decode('{ "meta": {
                        "uri": "TOTO",
                        "totalItems": 200,
                        "totalItemsIsExact": true,
                        "pageItems": 2,
                        "firstPageItem": 21,
                        "lastPageItem": 22,
                        "durationMs": 66,
                        "firstPaFId": 1,
                        "lastPaFId": 1,
                        "producer": "SEARCH" } }');
        $meta = new AfsMetaHelper($input->meta);
        $this->assertEquals($meta->get_feed(), 'TOTO');
        $this->assertEquals($meta->get_total_replies(), 200);
        $this->assertEquals($meta->get_replies_per_page(), 2);
        $this->assertEquals($meta->get_duration(), 66);
        $this->assertEquals($meta->get_producer(), 'SEARCH');
        $this->assertFalse($meta->has_cluster());
    }
    public function testValuesWithCluster()
    {
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
        $this->assertEquals('Catalog', $meta->get_feed());
        $this->assertEquals(61, $meta->get_total_replies());
        $this->assertEquals($meta->get_replies_per_page(), 20);
        $this->assertEquals(6, $meta->get_duration());
        $this->assertEquals('SEARCH', $meta->get_producer());
        $this->assertTrue($meta->has_cluster());
        $this->assertEquals('marketing', $meta->get_cluster_id());
        $this->assertEquals('marketing', $meta->get_cluster_label());
        $meta->set_cluster_label('My label');
        $this->assertEquals('My label', $meta->get_cluster_label());
    }

    public function testValuesAsArray()
    {
        $input = json_decode('{ "meta": {
                        "uri": "TOTO",
                        "totalItems": 200,
                        "totalItemsIsExact": true,
                        "pageItems": 2,
                        "firstPageItem": 21,
                        "lastPageItem": 22,
                        "durationMs": 66,
                        "firstPaFId": 1,
                        "lastPaFId": 1,
                        "producer": "SEARCH" } }');
        $meta = new AfsMetaHelper($input->meta);
        $meta = $meta->format();
        $this->assertTrue(array_key_exists('feed', $meta));
        $this->assertEquals($meta['feed'], 'TOTO');
        $this->assertTrue(array_key_exists('total_replies', $meta));
        $this->assertEquals($meta['total_replies'], 200);
        $this->assertTrue(array_key_exists('replies_per_page', $meta));
        $this->assertEquals($meta['replies_per_page'], 2);
        $this->assertTrue(array_key_exists('duration', $meta));
        $this->assertEquals($meta['duration'], 66);
        $this->assertTrue(array_key_exists('producer', $meta));
        $this->assertEquals($meta['producer'], 'SEARCH');
        $this->assertFalse(array_key_exists('cluster', $meta));
        $this->assertFalse(array_key_exists('cluster_label', $meta));
    }
    public function testValuesWithClusterAsArray()
    {
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
        $meta->set_cluster_label('My label');
        $meta = $meta->format();
        $this->assertTrue(array_key_exists('feed', $meta));
        $this->assertEquals($meta['feed'], 'Catalog');
        $this->assertTrue(array_key_exists('total_replies', $meta));
        $this->assertEquals($meta['total_replies'], 61);
        $this->assertTrue(array_key_exists('replies_per_page', $meta));
        $this->assertEquals($meta['replies_per_page'], 20);
        $this->assertTrue(array_key_exists('duration', $meta));
        $this->assertEquals($meta['duration'], 6);
        $this->assertTrue(array_key_exists('producer', $meta));
        $this->assertEquals($meta['producer'], 'SEARCH');
        $this->assertTrue(array_key_exists('cluster', $meta));
        $this->assertEquals($meta['cluster'], 'marketing');
        $this->assertTrue(array_key_exists('cluster_label', $meta));
        $this->assertEquals($meta['cluster_label'], 'My label');
    }
}


