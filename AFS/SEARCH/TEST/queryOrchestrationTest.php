<?php
require_once 'AFS/SEARCH/afs_search.php';
require_once 'COMMON/php-SAI/lib/CurlStub.php';
require_once 'AIF/afs_user_authentication.php';

class QueryOrchestrationTest extends PHPUnit_Framework_TestCase
{
    public function testAutoSpellcheckOrchestration()
    {
        $query = AfsQuery::create_from_parameters(array("query" => "topic", "mycustomparameter" => "mycustomvalue"));
        $curlConnector = new SAI_CurlStub();
        $mockBaseUrl = "localhost";
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
        $response = <<<JSON
{"header":{"query":{"userId":"b756a0de-e252-426f-8a24-66c4bbb96aa0","sessionId":"188ff0e1-5fd9-4a06-bab5-24ade9cab206","date":"2015-10-14T14:25:22+0000","queryParam":[{"name":"afs:service","value":"42"},{"name":"afs:status","value":"beta"},{"name":"afs:query","value":"vet"},{"name":"afs:output","value":"json,2"},{"name":"afs:facetDefault","value":"replies=100"},{"name":"afs:replies","value":"20"},{"name":"afs:sort","value":"disposite, DESC and afs:relevance, DESC"}],"mainCtx":{"textQuery":"vet"},"textQuery":"vet"},"user":{"requestMethod":"GET","agent":"Mozilla\/5.0 (X11; Linux x86_64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/42.0.2311.90 Safari\/537.36","address":"172.17.42.1","output":{"format":"JSON","encoding":"gzip","charset":"UTF-8"}},"performance":{"durationMs":7},"info":{},"orchestrationInfo":{"autoSpellchecker":{"triggered":true}}},"replySet":[{"meta":{"uri":"Catalog","totalItems":1,"totalItemsIsExact":true,"pageItems":1,"firstPageItem":1,"lastPageItem":1,"durationMs":1,"firstPaFId":3,"lastPaFId":3,"producer":"SEARCH"},"facets":{"facet":[{"afs:t":"FacetTree","node":[{"key":"fr","labels":[{"label":"Français"}],"items":1}],"layout":"TREE","type":"STRING","id":"afs:lang","labels":[{"label":"Language"}]}]},"content":{"reply":[{"docId":4,"uri":"3612531066812_fr","title":[{"afs:t":"KwicMatch","match":"vert"}],"relevance":{"rank":1},"clientData":[{"contents":"<product xmlns=\"http:\/\/ref.antidot.net\/store\/afs#\" xmlns:xsi=\"http:\/\/www.w3.org\/2001\/XMLSchema-instance\" id=\"3612531066812\" xml:lang=\"fr\" autocomplete=\"on\" xsi:schemaLocation=\"http:\/\/ref.antidot.net\/store\/afs# http:\/\/ref.antidot.net\/store\/v4.1\/xsd\/product.xsd\"><name xmlns=\"http:\/\/ref.antidot.net\/store\/afs#\">vert<\/name><\/product>","id":"main","mimeType":"text\/xml"}]}]}},{"meta":{"uri":"afs:spellcheck","totalItems":1,"totalItemsIsExact":true,"pageItems":1,"firstPageItem":1,"lastPageItem":1,"durationMs":0,"firstPaFId":1,"lastPaFId":1,"producer":"SPELLCHECK"},"content":{"reply":[{"docId":1,"uri":"Catalog","title":[{"afs:t":"KwicMatch","match":"vert"}],"abstract":[{"afs:t":"KwicString","text":"vert"}],"suggestion":[{"items":[{"match":{"text":"vert","src":"vet"}}]}]}]}}]}
JSON;


        //Set BO response for AboutConnector
        $curlConnector->setResponse($aboutResponse, $aboutRequestOpts);
        //Set response for query
        $curlConnector->setResponse($response);
        $search = new AfsSearch($mockBaseUrl, '71003', AfsServiceStatus::STABLE, $curlConnector);
        $search->set_query($query);
        $coder = new AfsQueryCoder();
        $search->set_query_coder($coder);
        $helper = $search->execute($query);
        $this->assertEquals($helper->is_orchestrated(), true);
        $this->assertEquals($helper->get_orchestration_type(), OrchestrationType::AUTOSPELLCHECKER);
    }

    public function testFallbackToOptionalOrchestration()
    {
        $query = AfsQuery::create_from_parameters(array("query" => "topic", "mycustomparameter" => "mycustomvalue"));
        $curlConnector = new SAI_CurlStub();
        $mockBaseUrl = "localhost";
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
        $response = <<<JSON

{"header":{"query":{"userId":"b756a0de-e252-426f-8a24-66c4bbb96aa0","sessionId":"188ff0e1-5fd9-4a06-bab5-24ade9cab206","date":"2015-10-14T14:24:09+0000","queryParam":[{"name":"afs:service","value":"42"},{"name":"afs:status","value":"beta"},{"name":"afs:query","value":"vert bleu"},{"name":"afs:output","value":"json,2"},{"name":"afs:facetDefault","value":"replies=100"},{"name":"afs:replies","value":"20"},{"name":"afs:sort","value":"disposite, DESC and afs:relevance, DESC"}],"mainCtx":{"textQuery":"vert bleu"},"textQuery":"vert bleu"},"user":{"requestMethod":"GET","agent":"Mozilla\/5.0 (X11; Linux x86_64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/42.0.2311.90 Safari\/537.36","address":"172.17.42.1","output":{"format":"JSON","encoding":"gzip","charset":"UTF-8"}},"performance":{"durationMs":5},"info":{},"orchestrationInfo":{"fallbackToOptional":{"triggered":true}}},"replySet":[{"meta":{"uri":"Catalog","totalItems":2,"totalItemsIsExact":true,"pageItems":2,"firstPageItem":1,"lastPageItem":2,"durationMs":0,"firstPaFId":3,"lastPaFId":3,"producer":"SEARCH"},"facets":{"facet":[{"afs:t":"FacetTree","node":[{"key":"fr","labels":[{"label":"Français"}],"items":2}],"layout":"TREE","type":"STRING","id":"afs:lang","labels":[{"label":"Language"}]}]},"content":{"reply":[{"docId":5,"uri":"36125310668123_fr","title":[{"afs:t":"KwicMatch","match":"bleu"}],"relevance":{"rank":1},"clientData":[{"contents":"<product xmlns=\"http:\/\/ref.antidot.net\/store\/afs#\" xmlns:xsi=\"http:\/\/www.w3.org\/2001\/XMLSchema-instance\" id=\"36125310668123\" xml:lang=\"fr\" autocomplete=\"on\" xsi:schemaLocation=\"http:\/\/ref.antidot.net\/store\/afs# http:\/\/ref.antidot.net\/store\/v4.1\/xsd\/product.xsd\"><name xmlns=\"http:\/\/ref.antidot.net\/store\/afs#\">bleu<\/name><\/product>","id":"main","mimeType":"text\/xml"}]},{"docId":4,"uri":"3612531066812_fr","title":[{"afs:t":"KwicMatch","match":"vert"}],"relevance":{"rank":2},"clientData":[{"contents":"<product xmlns=\"http:\/\/ref.antidot.net\/store\/afs#\" xmlns:xsi=\"http:\/\/www.w3.org\/2001\/XMLSchema-instance\" id=\"3612531066812\" xml:lang=\"fr\" autocomplete=\"on\" xsi:schemaLocation=\"http:\/\/ref.antidot.net\/store\/afs# http:\/\/ref.antidot.net\/store\/v4.1\/xsd\/product.xsd\"><name xmlns=\"http:\/\/ref.antidot.net\/store\/afs#\">vert<\/name><\/product>","id":"main","mimeType":"text\/xml"}]}]}}]}
JSON;


        //Set BO response for AboutConnector
        $curlConnector->setResponse($aboutResponse, $aboutRequestOpts);
        //Set response for query
        $curlConnector->setResponse($response);
        $search = new AfsSearch($mockBaseUrl, '71003', AfsServiceStatus::STABLE, $curlConnector);
        $search->set_query($query);
        $coder = new AfsQueryCoder();
        $search->set_query_coder($coder);
        $helper = $search->execute($query);
        $this->assertEquals($helper->is_orchestrated(), true);
        $this->assertEquals($helper->get_orchestration_type(), OrchestrationType::FALLBACKTOOPTIONAL);
    }

    /**
     * @expectedException Exception
     */
    public function testNotOrchestratedRequest()
    {
        $query = AfsQuery::create_from_parameters(array("query" => "topic", "mycustomparameter" => "mycustomvalue"));
        $curlConnector = new SAI_CurlStub();
        $mockBaseUrl = "localhost";
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
        $response = <<<JSON


{"header":{"query":{"userId":"b756a0de-e252-426f-8a24-66c4bbb96aa0","sessionId":"dd33b13c-dd9f-49a1-abe1-95e89140fb40","date":"2015-10-14T15:12:00+0000","queryParam":[{"name":"afs:service","value":"42"},{"name":"afs:status","value":"beta"},{"name":"afs:query","value":"vert"},{"name":"afs:output","value":"json,2"},{"name":"afs:facetDefault","value":"replies=100"},{"name":"afs:replies","value":"20"},{"name":"afs:sort","value":"disposite, DESC and afs:relevance, DESC"}],"mainCtx":{"textQuery":"vert"},"textQuery":"vert"},"user":{"requestMethod":"GET","agent":"Mozilla\/5.0 (X11; Linux x86_64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/42.0.2311.90 Safari\/537.36","address":"172.17.42.1","output":{"format":"JSON","encoding":"gzip","charset":"UTF-8"}},"performance":{"durationMs":5},"info":{}},"replySet":[{"meta":{"uri":"Catalog","totalItems":1,"totalItemsIsExact":true,"pageItems":1,"firstPageItem":1,"lastPageItem":1,"durationMs":1,"firstPaFId":3,"lastPaFId":3,"producer":"SEARCH"},"facets":{"facet":[{"afs:t":"FacetTree","node":[{"key":"fr","labels":[{"label":"Français"}],"items":1}],"layout":"TREE","type":"STRING","id":"afs:lang","labels":[{"label":"Language"}]}]},"content":{"reply":[{"docId":4,"uri":"3612531066812_fr","title":[{"afs:t":"KwicMatch","match":"vert"}],"relevance":{"rank":1},"clientData":[{"contents":"<product xmlns=\"http:\/\/ref.antidot.net\/store\/afs#\" xmlns:xsi=\"http:\/\/www.w3.org\/2001\/XMLSchema-instance\" id=\"3612531066812\" xml:lang=\"fr\" autocomplete=\"on\" xsi:schemaLocation=\"http:\/\/ref.antidot.net\/store\/afs# http:\/\/ref.antidot.net\/store\/v4.1\/xsd\/product.xsd\"><name xmlns=\"http:\/\/ref.antidot.net\/store\/afs#\">vert<\/name><\/product>","id":"main","mimeType":"text\/xml"}]}]}}]}
JSON;


        //Set BO response for AboutConnector
        $curlConnector->setResponse($aboutResponse, $aboutRequestOpts);
        //Set response for query
        $curlConnector->setResponse($response);
        $search = new AfsSearch($mockBaseUrl, '71003', AfsServiceStatus::STABLE, $curlConnector);
        $search->set_query($query);
        $coder = new AfsQueryCoder();
        $search->set_query_coder($coder);
        $helper = $search->execute($query);
        $this->assertEquals($helper->is_orchestrated(), false);

        $helper->get_orchestration_type();
    }
}