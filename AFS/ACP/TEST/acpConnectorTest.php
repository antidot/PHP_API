<?php ob_start();
require_once "AFS/ACP/afs_acp_connector.php";
require_once "AFS/ACP/afs_acp_query.php";



class Connector extends AfsAcpConnector
{
    public function __construct($host, $service)
    {
        parent::__construct($host, $service);
    }

    public function get_url()
    {
        return $this->scheme . '://' . $this->host . '/acp';
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
    public function build_url($web_service, array $parameters)
    {
        return parent::build_url('acp', $parameters);
    }
}

class AcpConnectorTest extends PHPUnit_Framework_TestCase
{
    public function testConstructDefaultParameters()
    {
        $connector = new Connector('url', new AfsService(42));
        $this->assertEquals('http://url/acp', $connector->get_url());
        $this->assertEquals(42, $connector->get_id());
        $this->assertEquals('stable', $connector->get_status());
    }
    public function testConstructParameters()
    {
        $connector = new Connector('url', new AfsService(42, 'rc'));
        $this->assertEquals('http://url/acp', $connector->get_url());
        $this->assertEquals(42, $connector->get_id());
        $this->assertEquals('rc', $connector->get_status());
    }

    public function testNoParameter()
    {
        $connector = new Connector('url', new AfsService(42));
        $this->assertEquals('', $connector->format_parameters(array()));
    }
    public function testParameters()
    {
        $connector = new Connector('url', new AfsService(42));
        $this->assertEquals('foo=bar&fooz=baz&fooz=bat',
            $connector->format_parameters(array(
            'foo' => 'bar',
            'fooz' => array('baz', 'bat'))));
    }

    public function testFailOnInvalidUrl()
    {
        $connector = new AfsAcpConnector('foo', new AfsService(42));
        try {
            $connector->send(array());
            $this->fail('Send query with bad URL should have failed!');
        } catch (Exception $e) { }
    }

    public function testAPIVersion()
    {
        $connector = new Connector('foo', new AfsService(42));
        $query = new AfsAcpQuery();
        $url = $connector->build_url(null, $query->get_parameters());
        $this->assertFalse(strpos($url, urlencode(get_api_version())) === False,
            '"'.urlencode(get_api_version()).'" should be in: '.$url);
    }

    public function testNoUserAgent()
    {
        $connector = new Connector('foo', new AfsService(42));
        $query = new AfsAcpQuery();
        $url = $connector->build_url(null, $query->get_parameters());
        $this->assertTrue(strpos($url, urlencode('afs:userAgent')) === False);
    }

    public function testUserAgent()
    {
        global $_SERVER;
        $_SERVER = array('HTTP_USER_AGENT' => 'foo');
        $connector = new Connector('foo', new AfsService(42));
        $query = new AfsAcpQuery();
        $url = $connector->build_url(null, $query->get_parameters());
        $this->assertFalse(strpos($url, urlencode('afs:userAgent')) === False);
    }

    public function testNoIp()
    {
        $connector = new Connector('foo', new AfsService(42));
        $query = new AfsAcpQuery();
        $url = $connector->build_url(null, $query->get_parameters());
        $this->assertTrue(strpos($url, urlencode('afs:ip')) === False);
    }

    public function testIp()
    {
        global $_SERVER;
        $_SERVER = array('REMOTE_ADDR' => '127.0.0.1');
        $connector = new Connector('foo', new AfsService(42));
        $query = new AfsAcpQuery();
        $url = $connector->build_url(null, $query->get_parameters());
        $this->assertFalse(strpos($url, urlencode('afs:ip')) === False);
    }
}


