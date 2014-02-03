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
        $this->assertEquals($meta->get_duration(), 66);
        $this->assertEquals($meta->get_producer(), 'SEARCH');
    }
}


