<?php ob_start();
require_once "AFS/SEARCH/afs_client_data_helper.php";

class ClientDataHelperTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveXMLClientDataAsText()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": "<clientdata><data><data1>data 0</data1><data1>data 1</data1><multi><m0>m 0</m0><m1>m 1</m1><m2>m 2</m2><m3>m 3</m3></multi></data></clientdata>",
                "id": "main",
                "mimeType": "text/xml"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals($helper->value, '<clientdata><data><data1>data 0</data1><data1>data 1</data1><multi><m0>m 0</m0><m1>m 1</m1><m2>m 2</m2><m3>m 3</m3></multi></data></clientdata>');
        $this->assertEquals($helper->mime_type, 'application/xml');
    }

    public function testRetrieveSpecificDataFromXMLClientData()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": "<clientdata><data><data1>data 0</data1><data1>data 1</data1><multi><m0>m 0</m0><m1>m 1</m1><m2>m 2</m2><m3>m 3</m3></multi></data></clientdata>",
                "id": "main",
                "mimeType": "text/xml"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals($helper->get_value('/clientdata/data/data1'), 'data 0');
        $this->assertEquals($helper->get_value('/clientdata/data/data1[2]'), 'data 1');
        $this->assertEquals(array('data 0', 'data 1'), $helper->get_values('/clientdata/data/data1'));
    }

    public function testRetrieveSpecificDataFromXMLClientDataWithNamedNamespace()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": "<clientdata xmlns:foo=\"http://bar\"><foo:data><data1>data 0</data1><data1>data 1</data1><multi><m0>m 0</m0><m1>m 1</m1><m2>m 2</m2><m3>m 3</m3></multi></foo:data></clientdata>",
                "id": "main",
                "mimeType": "text/xml"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals('data 0', $helper->get_value('/clientdata/boo:data/data1', array('boo' => 'http://bar')));
        $this->assertEquals('data 1', $helper->get_value('/clientdata/boo:data/data1[2]', array('boo' => 'http://bar')));
        $this->assertEquals(array('data 0', 'data 1'), $helper->get_values('/clientdata/boo:data/data1', array('boo' => 'http://bar')));
    }

    public function testRetrieveSpecificDataFromXMLClientDataWithDefaultNamespace()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": "<clientdata xmlns=\"http://bar\"><data><data1>data 0</data1><data1>data 1</data1><multi><m0>m 0</m0><m1>m 1</m1><m2>m 2</m2><m3>m 3</m3></multi></data></clientdata>",
                "id": "main",
                "mimeType": "text/xml"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals('data 0', $helper->get_value('/boo:clientdata/boo:data/boo:data1', array('boo' => 'http://bar')));
        $this->assertEquals('data 1', $helper->get_value('/boo:clientdata/boo:data/boo:data1[2]', array('boo' => 'http://bar')));
        $this->assertEquals(array('data 0', 'data 1'), $helper->get_values('/boo:clientdata/boo:data/boo:data1', array('boo' => 'http://bar')));
    }

    public function testRetrieveSpecificDataFromXMLClientDataWithAfsNamespace()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": "<clientdata xmlns=\"http://ref.antidot.net/v7/afs#\"><data><data1>data 0</data1><data1>data 1</data1><multi><m0>m 0</m0><m1>m 1</m1><m2>m 2</m2><m3>m 3</m3></multi></data></clientdata>",
                "id": "main",
                "mimeType": "text/xml"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals('data 0', $helper->get_value('/afs:clientdata/afs:data/afs:data1'));
    }

    public function testInvalidXpathForXmlClientDataRetrieval()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": "<clientdata><data><data1>data 0</data1><data1>data 1</data1><multi><m0>m 0</m0><m1>m 1</m1><m2>m 2</m2><m3>m 3</m3></multi></data></clientdata>",
                "id": "main",
                "mimeType": "text/xml"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        try {
            $helper->get_value('/clientdata/data/foo');
            $this->fail('XPath with no reply should have raised exception');
        } catch (AfsClientDataException $e) { }
    }

    public function testEmptyResultXpathForXmlClientDataRetrieval()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": "<clientdata><data></data></clientdata>",
                "id": "main",
                "mimeType": "text/xml"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals('', $helper->get_value('/clientdata/data'));
    }

    public function testRetrieveXmlClientDataWithHighlight()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": "<clientdata><data><data1>data <afs:match>0</afs:match></data1><data1>data <afs:match>1</afs:match> foo</data1></data></clientdata>",
                "id": "foo",
                "mimeType": "text/xml"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals($helper->get_value('/clientdata/data/data1[1]'), 'data <b>0</b>');
        $this->assertEquals($helper->get_value('/clientdata/data/data1[2]'), 'data <b>1</b> foo');
        $this->assertEquals(array('data <b>0</b>', 'data <b>1</b> foo'), $helper->get_values('/clientdata/data/data1'));
    }

    public function testRetrieveXmlClientDataWithTruncatedText()
    {
        $input = json_decode('{
            "clientData": [
              {
            "contents": "<clientdata><data><data1>data 0<afs:trunc/></data1><data1>data 1 foo<afs:trunc/></data1></data></clientdata>",
                "id": "foo",
                "mimeType": "text/xml"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals($helper->get_value('/clientdata/data/data1[1]'), 'data 0...');
        $this->assertEquals($helper->get_value('/clientdata/data/data1[2]'), 'data 1 foo...');
        $this->assertEquals(array('data 0...', 'data 1 foo...'), $helper->get_values('/clientdata/data/data1'));
    }

    public function testRetrieveJSONDataAsText()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": { "data": [ { "data1": [ { "afs:t": "KwicString", "text": "data 0" } ] }, { "data1": [ { "afs:t": "KwicString", "text": "data " }, { "afs:t": "KwicMatch", "match": "1" } ] } ] },
                "id": "id1",
                "mimeType": "application/json"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals($helper->value, '{"data":[{"data1":[{"afs:t":"KwicString","text":"data 0"}]},{"data1":[{"afs:t":"KwicString","text":"data "},{"afs:t":"KwicMatch","match":"1"}]}]}');
        $this->assertEquals($helper->mime_type, 'application/json');
    }

    public function testRetrieveSimpleJSONDataAsText2()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": [ { "afs:t": "KwicString", "text": "data 1" } ],
                "id": "id1",
                "mimeType": "application/json"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals($helper->get_value(''), 'data 1');
    }

    public function testRetrieveJSONDataAsTextWithHighlight()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": [ { "afs:t": "KwicString", "text": "data " }, { "afs:t": "KwicMatch", "match": "1" } ],
                "id": "id1",
                "mimeType": "application/json"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals($helper->get_value(''), 'data <b>1</b>');
    }

    public function testRetrieveSpecificJSONDataAsTextWithHighlight()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": { "foo": [ { "afs:t": "KwicString", "text": "data " },
                                       { "afs:t": "KwicMatch", "match": "1" } ],
                              "bar": [ { "afs:t": "KwicString", "text": "baz " },
                                       { "afs:t": "KwicMatch", "match": "42" },
                                       { "afs:t": "KwicString", "text": " bat" } ] },
                "id": "id1",
                "mimeType": "application/json"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        $this->assertEquals($helper->get_value('bar'), 'baz <b>42</b> bat');
    }

    public function testRetrieveUnknownSpecificJSONDataAsTextWithHighlight()
    {
        $input = json_decode('{
            "clientData": [
              {
                "contents": { "foo": [ { "afs:t": "KwicString", "text": "data " },
                                       { "afs:t": "KwicMatch", "match": "1" } ] },
                "id": "id1",
                "mimeType": "application/json"
              }
            ]
          }');
        $helper = AfsClientDataHelperFactory::create($input->clientData[0]);
        try {
            $helper->get_value('bar');
            $this->fail('Unknown JSON element should have rosen exception');
        } catch (AfsClientDataException $e) { }
    }


    public function testClientDataManagerDirectClientDataAccess()
    {
        $input = json_decode('[
              {
                "contents": { "foo": [ { "afs:t": "KwicString", "text": "data " },
                                       { "afs:t": "KwicMatch", "match": "1" } ],
                              "bar": [ { "afs:t": "KwicString", "text": "baz " },
                                       { "afs:t": "KwicMatch", "match": "42" },
                                       { "afs:t": "KwicString", "text": " bat" } ] },
                "id": "id1",
                "mimeType": "application/json"
              },
              {
                "contents": "<clientdata><data><data1>data <afs:match>0</afs:match></data1><data1>data <afs:match>1</afs:match> foo</data1></data></clientdata>",
                "id": "foo",
                "mimeType": "text/xml"
              }
            ]');
        $mgr = new AfsClientDataManager($input);
        $this->assertEquals($mgr->get_value('id1', 'bar'), 'baz <b>42</b> bat');
        $this->assertEquals($mgr->get_value('foo', '/clientdata/data/data1[1]'), 'data <b>0</b>');
        $this->assertEquals($mgr->get_value('foo', '/clientdata/data/data1[2]'), 'data <b>1</b> foo');
        $this->assertEquals(array('data <b>0</b>', 'data <b>1</b> foo'), $mgr->get_values('foo', '/clientdata/data/data1'));
    }

    public function testClientDataManagerFirstRetrieveClientDataHelpers()
    {
        $input = json_decode('[
              {
                "contents": { "foo": [ { "afs:t": "KwicString", "text": "data " },
                                       { "afs:t": "KwicMatch", "match": "1" } ],
                              "bar": [ { "afs:t": "KwicString", "text": "baz " },
                                       { "afs:t": "KwicMatch", "match": "42" },
                                       { "afs:t": "KwicString", "text": " bat" } ] },
                "id": "id1",
                "mimeType": "application/json"
              },
              {
                "contents": "<clientdata><data><data1>data <afs:match>0</afs:match></data1><data1>data <afs:match>1</afs:match> foo</data1></data></clientdata>",
                "id": "foo",
                "mimeType": "text/xml"
              }
            ]');
        $mgr = new AfsClientDataManager($input);
        $data1 = $mgr->get_clientdata('id1');
        $this->assertEquals('data <b>1</b>', $data1->get_value('foo'));

        $data2 = $mgr->get_clientdata('foo');
        $this->assertEquals('data <b>0</b>', $data2->get_value('/clientdata/data/data1[1]'));
    }

    public function testRawXmlClientDataWithNamespace()
    {
        $input = json_decode('[
              {
                "contents": "<clientdata><data><data1>data <afs:match>0</afs:match></data1><data1>data <afs:match>1</afs:match> foo</data1></data></clientdata>",
                "id": "foo",
                "mimeType": "text/xml"
              }
            ]');
        $mgr = new AfsClientDataManager($input);
        $data = $mgr->get_clientdata('foo')->get_value();
        $doc = new DOMDocument();
        $doc->loadXML($data);
    }

    public function testJsonCltDataGetValueJsonClientDataHelper() {
        $input = json_decode('
              {
                "contents": { "data1": "value1",
                              "data2": [ { "k": "v" } ] },
                "id": "id1",
                "mimeType": "application/json"
              }');


        $json_clientdata = new AfsJsonClientDataHelper($input);
        $this->assertEquals('value1', $json_clientdata->get_node("$.data1"));
        $this->assertEquals('v', $json_clientdata->get_node("$.data2[0].k"));

        $expected_result = array("data1" => "value1", "data2" => array(array("k" => "v")));
        $this->assertEquals(array($expected_result),
                $json_clientdata->get_nodes(""));
        $this->assertEquals(array($expected_result),
            $json_clientdata->get_nodes());
        $this->assertEquals($expected_result,
            $json_clientdata->get_node(""));
        $this->assertEquals($expected_result,
            $json_clientdata->get_node());
    }

    public function testJsonCltDataFirstElementReturnedByGetNode() {
        $input = json_decode('
              {
                "contents": {
                              "data1": [ { "k": "v" } ], "data1": "value1" },
                "id": "id1",
                "mimeType": "application/json"
              }');

        $json_clientdata = new AfsJsonClientDataHelper($input);
        $this->assertEquals('value1', $json_clientdata->get_node("$.data1"));
    }

    public function testJsonCltDataMultipleElementsReturnedByGetNodes() {
        $input = json_decode('
              {
                "contents": { "data1": "value1",
                              "data": { "data1": [ { "k": "v" } ] }},
                "id": "id1",
                "mimeType": "application/json"
              }');

        $json_clientdata = new AfsJsonClientDataHelper($input);
        $this->assertTrue(in_array('value1', $json_clientdata->get_nodes("$..data1")));
        $this->assertTrue(in_array(array(array("k" => "v")), $json_clientdata->get_nodes("$..data1")));
    }

    /**
     * @expectedException AfsNoResultException
     */
    public function testJsonCltDataNoElementFoundGetNode() {
        $input = json_decode('
              {
                "contents": { "dataa1": "value1",
                              "dataa2": [ { "k": "v" } ] },
                "id": "id1",
                "mimeType": "application/json"
              }');
        $json_clientdata = new AfsJsonClientDataHelper($input);
        $json_clientdata->get_node("$.data1");
    }

    /**
     * @expectedException AfsNoResultException
     */
    public function testJsonCltDataNoElementFoundgetNodes() {
        $input = json_decode('
              {
                "contents": { "data1": "value1",
                              "data2": [ { "k": "v" } ] },
                "id": "id1",
                "mimeType": "application/json"
              }');

        $input = json_decode('
              {
                "contents": { "dataa1": "value1",
                              "dataa2": [ { "k": "v" } ] },
                "id": "id1",
                "mimeType": "application/json"
              }');
        $json_clientdata = new AfsJsonClientDataHelper($input);
        $json_clientdata->get_nodes("$.data1");
    }

    public function testXmlCltDataGetNodeDataHelper() {
        $xml_client_data = '<clientData><data1>value1</data1><data2><k>v</k></data2></clientData>';
        $input = json_decode('
              {
                "contents": "' . $xml_client_data . '",
                "id": "id1",
                "mimeType": "application/json"
              }');


        $xml_clientdata = new AfsXmlClientDataHelper($input);
        $this->assertEquals(array('data1' => 'value1'), $xml_clientdata->get_node("/clientData/data1"));
        $this->assertEquals(array('data2' => array('k' => 'v')), $xml_clientdata->get_node("/clientData/data2"));

        $expected_result = array('clientData' => array("data1" => "value1", "data2" => array("k" => "v")));
        $this->assertEquals(array($expected_result),
            $xml_clientdata->get_nodes(""));
        $this->assertEquals(array($expected_result),
            $xml_clientdata->get_nodes());
        $this->assertEquals($expected_result,
            $xml_clientdata->get_node(""));
        $this->assertEquals($expected_result,
            $xml_clientdata->get_node());
    }

    public function testXmlCltDataFirstElementReturnedByGetNode() {
        $xml_client_data = '<clientData><data1>value1</data1><data1><k>v</k></data1></clientData>';
        $input = json_decode('
              {
                "contents": "' . $xml_client_data . '",
                "id": "id1",
                "mimeType": "application/json"
              }');

        $xml_clientdata = new AfsXmlClientDataHelper($input);
        $this->assertEquals(array('data1' => 'value1'), $xml_clientdata->get_node("/clientData/data1"));
    }

    public function testXmlCltDataMultipleElementsReturnedByGetNodes() {
        $xml_client_data = '<clientData><data1>value1</data1><data1><k>v</k></data1></clientData>';
        $input = json_decode('
              {
                "contents": "' . $xml_client_data . '",
                "id": "id1",
                "mimeType": "application/json"
              }');

        $xml_clientdata = new AfsXmlClientDataHelper($input);
        $this->assertTrue(in_array(array('data1' => 'value1'), $xml_clientdata->get_nodes("//data1")));
        $this->assertTrue(in_array(array("data1" => array("k" => "v")), $xml_clientdata->get_nodes("//data1")));
    }

    /**
     * @expectedException AfsNoResultException
     */
    public function testXmlCltDataNoElementFoundGetNode() {
        $xml_client_data = '<clientData><data1>value1</data1><data1><k>v</k></data1></clientData>';
        $input = json_decode('
              {
                "contents": "' . $xml_client_data . '",
                "id": "id1",
                "mimeType": "application/json"
              }');

        $xml_clientdata = new AfsXmlClientDataHelper($input);
        $xml_clientdata->get_node("/foo");
    }

    /**
     * @expectedException AfsNoResultException
     */
    public function testXmlCltDataNoElementFoundgetNodes() {
        $xml_client_data = '<clientData><data1>value1</data1><data1><k>v</k></data1></clientData>';
        $input = json_decode('
              {
                "contents": "' . $xml_client_data . '",
                "id": "id1",
                "mimeType": "application/json"
              }');

        $xml_clientdata = new AfsXmlClientDataHelper($input);
        $xml_clientdata->get_node("/foo");
    }
}
