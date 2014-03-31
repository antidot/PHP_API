<?php
/** @file pafLiveTest.php */

require_once('AIF/afs_paf_live_connector.php');
require_once("COMMON/php-SAI/lib/CurlStub.php");

class PafLiveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers AfsPafLiveConnector::process_doc
     * @covers AfsPafLiveConnector::__construct
     */
    public function testProcess()
    {
        $mockBaseUrl = "url";
        $auth = new AfsUserAuthentication('t8', 'antidot', AFS_AUTH_BOWS);
        $service = new AfsService(80108);
        $aboutRequestOpts = array(CURLOPT_URL => "http://$mockBaseUrl/bo-ws/about");
        $aboutResponse = <<<JSON
{
  "x:type":"ws.response",
  "query":{
    "x:type":"ws.response.query",
    "parameters":{
      "x:type":"collection",
      "x:values":[

      ]
    },
    "properties":{
      "x:type":"x:dynamic"
    }
  },
  "result":{
    "x:type":"bows.about",
    "boWsVersion":{
      "x:type":"AfsVersion",
      "build":"3eaebfd1f1fe261780347cbc35bfbd65d613575e",
      "gen":"7.6",
      "major":"4",
      "minor":"0",
      "motto":"Pink Dolphin"
    },
    "copyright":"Copyright (C) 1999-2013 Antidot"
  }
}
JSON;
        $testContent = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
<test>Francois Hollande mange des flamby</test>
</root>
XML;
        $testDocument = new AfsDocument($testContent, 'text/xml');
        $pafLiveResponse = <<<XML
-----Aa1Bb2Cc3---\r
Content-Disposition: form-data; name="urn:afs:6d57294f-1cd6-4220-8e10-e8ee9b518c78#CONTENTS"; filename=""\r
Content-Type: text/xml\r
\r
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<root>
<test>Francois Hollande mange des flamby</test>
</root>\r
-----Aa1Bb2Cc3---\r
Content-Disposition: form-data; name="urn:afs:6d57294f-1cd6-4220-8e10-e8ee9b518c78#USER_1"; filename=""\r
Content-Type: application/xml\r
\r
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<afs:Entities xmlns:afs="http://ref.antidot.net/v7/afs#"><afs:entity type="PERSON" text="Francois Hollande" count="1" confidence="0.99"></afs:entity></afs:Entities>\r
-----Aa1Bb2Cc3-----\r

XML;
        $curlConnector = new SAI_CurlStub();
        //Set BO response for AboutConnector
        $curlConnector->setResponse($aboutResponse, $aboutRequestOpts);
        //Set response for other requests
        $curlConnector->setResponse($pafLiveResponse);
        $paf = new AfsPafLiveConnector($mockBaseUrl, $service, "pafName", $auth, AFS_SCHEME_HTTP, $curlConnector);
        $response = $paf->process_doc($testDocument);
        $contents = $response["CONTENTS"];
        $user = $response["USER_1"];
        $this->assertEquals("text/xml", $contents->get_mime_type());
        $layer_content = $contents->get_content();
        $this->assertFalse(empty($layer_content));
        $xml = simplexml_load_string($layer_content);
        $this->assertEquals("Francois Hollande mange des flamby", $xml->test[0]);
        $layer_content = $user->get_content();
        $this->assertEquals("application/xml", $user->get_mime_type());
        $this->assertFalse(empty($layer_content));
        $xml = simplexml_load_string($layer_content, "SimpleXMLElement", 0, "afs", true);
        $this->assertEquals("Francois Hollande", $xml->entity[0]->attributes()->text);
    }
}