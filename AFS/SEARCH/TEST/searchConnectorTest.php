<?php ob_start();
require_once "AFS/SEARCH/afs_search_connector.php";
require_once "AFS/SEARCH/afs_query.php";



class Connector extends AfsSearchConnector
{
    public function __construct($host, $service)
    {
        parent::__construct($host, $service);
    }

    public function get_url()
    {
        return $this->scheme . '://' . $this->host . '/search';
    }
    public function get_id()
    {
        return $this->service->id;
    }
    public function get_status()
    {
        return $this->service->status;
    }
    public function format_parameters(array $parameters)
    {
        return parent::format_parameters($parameters);
    }
    public function build_url(array $parameters)
    {
        return parent::build_url($parameters);
    }
}

class SearchConnectorTest extends PHPUnit_Framework_TestCase
{
    public function testConstructDefaultParameters()
    {
        $connector = new Connector('url', new AfsService(42));
        $this->assertEquals($connector->get_url(), 'http://url/search');
        $this->assertTrue($connector->get_id() == 42);
        $this->assertTrue($connector->get_status() == 'stable');
    }
    public function testConstructParameters()
    {
        $connector = new Connector('url', new AfsService(42, 'rc'));
        $this->assertEquals($connector->get_url(), 'http://url/search');
        $this->assertTrue($connector->get_id() == 42);
        $this->assertTrue($connector->get_status() == 'rc');
    }

    public function testNoParameter()
    {
        $connector = new Connector('url', new AfsService(42));
        $this->assertEquals($connector->format_parameters(array()), '');
    }
    public function testParameters()
    {
        $connector = new Connector('url', new AfsService(42));
        $this->assertEquals($connector->format_parameters(array(
            'foo' => 'bar',
            'fooz' => array('baz', 'bat'))),
            'foo=bar&fooz=baz&fooz=bat');
    }

    public function testFailOnInvalidUrl()
    {
        $connector = new AfsSearchConnector('foo', new AfsService(42));
        try {
            $connector->send(array());
            $this->fail('Send query with bad URL should have failed!');
        } catch (Exception $e) { }
    }

    public function testAPIVersion()
    {
        $connector = new Connector('foo', new AfsService(42));
        $query = new AfsQuery();
        $url = $connector->build_url($query->get_parameters());
        $this->assertFalse(strpos($url, urlencode(get_api_version())) === False,
            '"'.urlencode(get_api_version()).'" should be in: '.$url);
    }

    public function testNoUserAgent()
    {
        $connector = new Connector('foo', new AfsService(42));
        $query = new AfsQuery();
        $url = $connector->build_url($query->get_parameters());
        $this->assertTrue(strpos($url, urlencode('afs:userAgent')) === False);
    }

    public function testUserAgent()
    {
        global $_SERVER;
        $_SERVER = array('HTTP_USER_AGENT' => 'foo');
        $connector = new Connector('foo', new AfsService(42));
        $query = new AfsQuery();
        $url = $connector->build_url($query->get_parameters());
        $this->assertFalse(strpos($url, urlencode('afs:userAgent')) === False);
    }

    public function testNoIp()
    {
        $connector = new Connector('foo', new AfsService(42));
        $query = new AfsQuery();
        $url = $connector->build_url($query->get_parameters());
        $this->assertTrue(strpos($url, urlencode('afs:ip')) === False);
    }

    public function testIp()
    {
        global $_SERVER;
        $_SERVER = array('REMOTE_ADDR' => '127.0.0.1');
        $connector = new Connector('foo', new AfsService(42));
        $query = new AfsQuery();
        $url = $connector->build_url($query->get_parameters());
        $this->assertFalse(strpos($url, urlencode('afs:ip')) === False);
    }
}


