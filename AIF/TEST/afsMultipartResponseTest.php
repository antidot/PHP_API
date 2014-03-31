<?php
/** @file afsMultipartResponseTest.php */

require_once('AIF/afs_multipart_response.php');

class AfsMultipartResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers AfsMultipartResponse::parse_headers
     * @covers AfsMultipartResponse::__construct
     * @covers AfsMultipartResponse::get_layer
     * @covers AfsMultipartResponse::get_layers
     * @covers AfsLayer::__construct
     * @covers AfsLayer::get_header
     * @covers AfsLayer::get_mime_type
     * @covers AfsLayer::get_content
     */
    public function testProcess()
    {
        $rawSinglePart = <<<HTTP
Content-Type: text/xml+application/json\r
Content-Disposition: name="urn:afs:toto-is-1n-7he-kitc4en#VALIDLAYER_1"\r
Accept: toto\r
Header: value\r

contents
HTTP;
        $rawMultipart = <<<HTTP
---70to---\r
Content-Type: text/xml\r
Content-Disposition: name="urn:afs:totois1n7hekitc4en#VALIDLAYER_2"\r
\r
contents1\r
---70to---\r
Content-Type: application/json\r
Content-Disposition: name="urn:afs:totois1n7hekitc4en#VALIDLAYER_3"\r
\r
contents2\r
---70to-----\r
HTTP;
        $singlePart = new AfsMultipartResponse($rawSinglePart);
        $this->assertEquals("contents", $singlePart->get_layer("VALIDLAYER_1")->get_content());
        $this->assertEquals("text/xml+application/json", $singlePart->get_layer('VALIDLAYER_1')->get_mime_type());
        $this->assertEquals("toto", $singlePart->get_layer("VALIDLAYER_1")->get_header("Accept"));
        $multiPart = new AfsMultipartResponse($rawMultipart);
        $this->assertEquals("contents1", $multiPart->get_layer("VALIDLAYER_2")->get_content());
        $this->assertEquals("contents2", $multiPart->get_layer("VALIDLAYER_3")->get_content());
        $this->assertEquals("text/xml", $multiPart->get_layers()['VALIDLAYER_2']->get_mime_type());
        $this->assertEquals("application/json", $multiPart->get_layers()['VALIDLAYER_3']->get_mime_type());
    }
}