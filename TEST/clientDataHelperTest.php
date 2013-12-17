<?php
require_once "afs_client_data_helper.php";

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
        $this->assertEquals($helper->text, '<clientdata><data><data1>data 0</data1><data1>data 1</data1><multi><m0>m 0</m0><m1>m 1</m1><m2>m 2</m2><m3>m 3</m3></multi></data></clientdata>');
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
        $this->assertEquals($helper->get_text('/clientdata/data/data1'), 'data 0');
        $this->assertEquals($helper->get_text('/clientdata/data/data1[2]'), 'data 1');
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
        $this->assertEquals('data 0', $helper->get_text('/clientdata/boo:data/data1', array('boo' => 'http://bar')));
        $this->assertEquals('data 1', $helper->get_text('/clientdata/boo:data/data1[2]', array('boo' => 'http://bar')));
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
        $this->assertEquals('data 0', $helper->get_text('/boo:clientdata/boo:data/boo:data1', array('boo' => 'http://bar')));
        $this->assertEquals('data 1', $helper->get_text('/boo:clientdata/boo:data/boo:data1[2]', array('boo' => 'http://bar')));
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
        $this->assertEquals('data 0', $helper->get_text('/afs:clientdata/afs:data/afs:data1'));
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
        $this->assertEquals($helper->get_text('/clientdata/data/foo'), '');
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
        $this->assertEquals($helper->get_text('/clientdata/data/data1[1]'), 'data <b>0</b>');
        $this->assertEquals($helper->get_text('/clientdata/data/data1[2]'), 'data <b>1</b> foo');
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
        $this->assertEquals($helper->text, '{"data":[{"data1":[{"afs:t":"KwicString","text":"data 0"}]},{"data1":[{"afs:t":"KwicString","text":"data "},{"afs:t":"KwicMatch","match":"1"}]}]}');
        $this->assertEquals($helper->mime_type, 'application/json');
    }

    public function testRetrieveSimpleJSONDataAsText()
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
        $this->assertEquals($helper->get_text(''), 'data 1');
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
        $this->assertEquals($helper->get_text(''), 'data <b>1</b>');
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
        $this->assertEquals($helper->get_text('bar'), 'baz <b>42</b> bat');
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
        $this->assertEquals($helper->get_text('bar'), '');
    }


    public function testClientDataManager()
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
              },
              {
                "contents": "<clientdata><data><data1>data <afs:match>0</afs:match></data1><data1>data <afs:match>1</afs:match> foo</data1></data></clientdata>",
                "id": "foo",
                "mimeType": "text/xml"
              }
            ]
          }');
        $mgr = new AfsClientDataManager($input);
        $this->assertEquals($mgr->get_text('id1', 'bar'), 'baz <b>42</b> bat');
        $this->assertEquals($mgr->get_text('foo', '/clientdata/data/data1[1]'), 'data <b>0</b>');
        $this->assertEquals($mgr->get_text('foo', '/clientdata/data/data1[2]'), 'data <b>1</b> foo');
    }
}

?>
