<?php ob_start();
require_once 'AFS/ACP/afs_acp.php';

class AcpTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveDefaultParameters()
    {
        $acp = new AfsAcp('127.0.0.1', 666);

        $service = $acp->get_service();
        $this->assertEquals(666, $service->id);
        $this->assertEquals(AfsServiceStatus::STABLE, $service->status);

        $acp->execute();
        $url = $acp->get_generated_url();
        $this->assertTrue(strpos($url, '127.0.0.1') !== False, 'URL does not contain right host');
        $this->assertTrue(strpos($url, 'service=666') !== False, 'URL does not contain right sesrvice id');
        $this->assertTrue(strpos($url, 'status=stable') !== False, 'URL does not contain right sesrvice status');

        $config = $acp->get_helpers_configuration();
        $this->assertEquals(AfsHelperFormat::ARRAYS, $config->get_helper_format());
    }

    public function testQueryStringOnly()
    {
        $acp = new AfsAcp('127.0.0.1', 666);
        $acp->query('foo');

        $query = $acp->get_query();
        $this->assertEquals('foo', $query->get_query());
        $this->assertFalse($query->has_feed());
    }
    public function testQueryStringWithFeeds()
    {
        $acp = new AfsAcp('127.0.0.1', 666);
        $acp->query('foo', array('a', 'b'));

        $query = $acp->get_query();
        $this->assertEquals('foo', $query->get_query());
        $this->assertTrue($query->has_feed());
        
        $feeds = $query->get_feeds();
        $feed = reset($feeds);
        $this->assertEquals('a', $feed);

        $feed = next($feeds);
        $this->assertEquals('b', $feed);
    }
    public function testQueryStringWithFeedsWhereasFeedsHasAlreadyBeenSet()
    {
        $acp = new AfsAcp('127.0.0.1', 666);
        $query = $acp->get_query();
        $query = $query->set_query('bar')->set_feed('bat')->add_feed('baz');
        $acp->query('foo', array('a', 'b'));

        $query = $acp->get_query();
        $this->assertEquals('foo', $query->get_query());
        $this->assertTrue($query->has_feed());
        
        $feeds = $query->get_feeds();
        $feed = reset($feeds);
        $this->assertEquals('a', $feed);

        $feed = next($feeds);
        $this->assertEquals('b', $feed);
    }
}
