<?php ob_start();
require_once 'AFS/SEARCH/afs_search.php';
require_once 'COMMON/php-SAI/lib/CurlStub.php';
require_once 'AIF/afs_user_authentication.php';

class SearchTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveDefaultParameters()
    {
        $search = new AfsSearch('127.0.0.1', 666);


        $service = $search->get_service();
        $this->assertEquals(666, $service->id);
        $this->assertEquals(AfsServiceStatus::STABLE, $service->status);

        $search->execute();
        $url = $search->get_generated_url();
        $this->assertTrue(strpos($url, '127.0.0.1') !== False, 'URL does not contain right host');
        $this->assertTrue(strpos($url, 'service=666') !== False, 'URL does not contain right sesrvice id');
        $this->assertTrue(strpos($url, 'status=stable') !== False, 'URL does not contain right sesrvice status');

        $config = $search->get_helpers_configuration();
        $this->assertEquals(AfsHelperFormat::HELPERS, $config->get_helper_format());
    }

    public function testBuildFromUrl() {
        $search = new AfsSearch('127.0.0.1', 666);

        $_SERVER['QUERY_STRING'] = 'filter=key_val&filter=k_v';
        $query = $search->build_query_from_url_parameters();

        $this->assertTrue($query->has_filter('key', 'val'));
        $this->assertTrue($query->has_filter('k', 'v'));

        $_SERVER['QUERY_STRING'] = 'filter=key_val-k_v';
        $query = $search->build_query_from_url_parameters();

        $this->assertTrue($query->has_filter('key', 'val'));
        $this->assertTrue($query->has_filter('k', 'v'));
    }

    public function testRetrieveSpecificParameters()
    {
        $search = new AfsSearch('127.0.0.2', 42, AfsServiceStatus::RC);

        $service = $search->get_service();
        $this->assertEquals(42, $service->id);
        $this->assertEquals(AfsServiceStatus::RC, $service->status);

        $search->execute(AfsHelperFormat::ARRAYS);
        $url = $search->get_generated_url();
        $this->assertTrue(strpos($url, '127.0.0.2') !== False, 'URL does not contain right host');
        $this->assertTrue(strpos($url, 'service=42') !== False, 'URL does not contain right sesrvice id');
        $this->assertTrue(strpos($url, 'status=rc') !== False, 'URL does not contain right sesrvice status');

        $config = $search->get_helpers_configuration();
        $this->assertEquals(AfsHelperFormat::ARRAYS, $config->get_helper_format());
    }

    public function testEncodedUrlRobustness() {
        $_SERVER['QUERY_STRING'] = urlencode('query=perles&filter=marketing_"is|_promotional"&filter=price|_eur_"[0 .. 1]"');

        $search = new AfsSearch('127.0.0.2', 42, AfsServiceStatus::RC);
        $query = $search->build_query_from_url_parameters();


        $this->assertTrue($query->has_filter('marketing', "\"is_promotional\""));
        $this->assertTrue($query->has_filter('price_eur', "\"[0 .. 1]\""));
    }

    public function testSetQuery()
    {
        $search = new AfsSearch('127.0.0.1', 666);
        $query = new AfsQuery();
        $query = $query->set_query('foo');
        $search->set_query($query);

        $this->assertEquals('foo', $search->get_query()->get_query());

        $search->execute();
        $this->assertTrue(strpos($search->get_generated_url(), 'query=foo') !== False, 'URL does not contain query!');
    }

    public function testBuildQueryFromUrlParametersType() {
        $search = new AfsSearch('127.0.0.1', 666);

        $_SERVER['QUERY_STRING'] = 'page=10&replies=100';
        $query = $search->build_query_from_url_parameters();

        $this->assertTrue(is_int($query->get_replies()));
        $this->assertTrue(is_int($query->get_page()));
        $this->assertEquals(10, $query->get_page());
        $this->assertEquals(100, $query->get_replies());
    }

    public function testSetFeedFilter() {
        $search = new AfsSearch('127.0.0.1', 666);
        $query = new AfsQuery();
        $query = $query->set_filter_on_feed('foo', array('bar'), 'feed');
        $search->set_query($query);

        $search->execute();
        $this->assertTrue(strpos($search->get_generated_url(), 'filter%40feed=foo%3Dbar') !== False, 'URL does not contain query!');
    }

    //Ensure custom parameters are kept
    public function testKeepCustomParameters()
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
{"header":{"query":{"userId":"user_5354ec142aa12","sessionId":"session_5354ec142aa4b","date":"2014-04-21T13:41:10+0200",
"queryParam":[{"name":"afs:service","value":"71003"},{"name":"afs:status","value":"beta"},{"name":"afs:query","value":"topic"},
{"name":"afs:query@Book","value":"topic"},{"name":"afs:query@Topic","value":"topic"},
{"name":"afs:query@spellcheck","value":"topic"},{"name":"afs:output","value":"json,2"},{"name":"afs:output@Book","value":"json,2"},
{"name":"afs:output@Topic","value":"json,2"},{"name":"afs:output@spellcheck","value":"json,2"},{"name":"mycustomparameter","value":"mycustomvalue"},
{"name":"mycustomparameter@Book","value":"mycustomvalue"},{"name":"mycustomparameter@Topic","value":"mycustomvalue"},
{"name":"mycustomparameter@spellcheck","value":"mycustomvalue"},{"name":"afs:replies","value":"10"},
{"name":"afs:replies@Book","value":"10"},{"name":"afs:replies@Topic","value":"10"},{"name":"afs:replies@spellcheck",
"value":"10"}],"mainCtx":{"textQuery":"topic"},"textQuery":"topic"},"user":{"requestMethod":"GET","agent":"Mozilla 5.0",
"address":"127.0.0.1","output":{"format":"JSON","encoding":"gzip","charset":"UTF-8"}},"performance":{"durationMs":11},"info":{}},
"replySet":[{"meta":{"uri":"Book","totalItems":1,"totalItemsIsExact":true,"pageItems":1,"firstPageItem":1,"lastPageItem":1,
"durationMs":1,"firstPaFId":147,"lastPaFId":147,"producer":"SEARCH"},
"facets":{"facet":[{"afs:t":"FacetTree","node":[{"key":"urn:dita:single_topic.ditamap",
"labels":[{"lang":"EN","label":"urn:dita:single_topic.ditamap"},{"lang":"FR","label":"urn:dita:single_topic.ditamap"}],
"items":1}],"layout":"TREE","type":"STRING","id":"BaseUri","labels":[{"lang":"EN","label":"BaseUri"},
{"lang":"FR","label":"BaseUri"}]},{"afs:t":"FacetTree","node":[{"key":"noditaval","labels":[{"lang":"EN","label":"noditaval"},
{"lang":"FR","label":"noditaval"}],"items":1}],"layout":"TREE","type":"STRING","id":"Ditaval",
"labels":[{"lang":"EN","label":"Ditaval"},{"lang":"FR","label":"Ditaval"}]},{"afs:t":"FacetTree",
"node":[{"key":"en","labels":[{"label":"English"}],"items":1}],"layout":"TREE","type":"STRING","id":"afs:lang",
"labels":[{"label":"Language"}]}]},"content":{"reply":[{"docId":3,"uri":"urn:dita:single_topic.ditamap",
"title":[{"afs:t":"KwicString","text":"Single "},{"afs:t":"KwicMatch","match":"Topic"},
{"afs:t":"KwicString","text":" Map"}],"relevance":{"rank":1},"layerReplies":{"reply":[{"layer":"USER_1",
"reply":{"docId":3,"uri":"","clientData":[{"contents":{"meta":[],"label":"Single Topic Map","ditaval":"noditaval",
"uri":"urn:dita:single_topic.ditamap"},"id":"ditaval","mimeType":"application\/json"}]}}]}}]}},
{"meta":{"uri":"Topic","totalItems":1,"totalItemsIsExact":true,"pageItems":1,"firstPageItem":1,"lastPageItem":1,"durationMs":2,
"firstPaFId":147,"lastPaFId":147,"producer":"SEARCH"},"facets":{"facet":[{"afs:t":"FacetTree",
"node":[{"key":"others","labels":[{"lang":"EN","label":"others"},{"lang":"FR","label":"others"}],"items":1}],
"layout":"TREE","type":"STRING","id":"Audience","labels":[{"lang":"EN","label":"Audience"},{"lang":"FR","label":"Audience"}]},
{"afs:t":"FacetTree","node":[{"key":"Single Topic Map","labels":[{"lang":"EN","label":"Single Topic Map"},
{"lang":"FR","label":"Single Topic Map"}],"items":1}],"layout":"TREE","type":"STRING","id":"Filter_By_Docs",
"labels":[{"lang":"EN","label":"Filter by Documents"},{"lang":"FR","label":"Filtrer par Documents"}]},
{"afs:t":"FacetTree","node":[{"key":"en","labels":[{"label":"English"}],"items":1}],"layout":"TREE","type":"STRING",
"id":"afs:lang","labels":[{"label":"Language"}]}]},"content":{"reply":[{"docId":2,"uri":"urn:dita:single_topic.dita",
"title":[{"afs:t":"KwicString","text":"Titre du "},{"afs:t":"KwicMatch","match":"topic"},
{"afs:t":"KwicString","text":" unique"}],"abstract":[{"afs:t":"KwicString","text":"Ce "},
{"afs:t":"KwicMatch","match":"topic"},{"afs:t":"KwicString","text":" est seul dans sa ditamap et ceci devrait apparaitre dans le résumé."}],
"relevance":{"rank":1},"layerReplies":{"reply":[{"layer":"USER_2","reply":{"docId":2,"uri":"",
"clientData":[{"contents":{"book":{"uri":"urn:dita:single_topic.ditamap","label":"Single Topic Map"},
"topics":[{"uri":"urn:dita:single_topic.dita","label":"Titre du topic unique"}],"ditaval":"noditaval"},
"id":"breadcrumb","mimeType":"application\/json"}]}}]}}]}}]}
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
        $replysetHelper = $helper->get_replyset("Book");
        $facetHelpers = $replysetHelper->get_facets();
        //Make sure each link of facets contains custom parameter
        foreach($facetHelpers as $facetHelper) {
            foreach($facetHelper->get_elements() as $facetValueHelper) {
                $this->assertEquals(1, preg_match("/[&\?]mycustomparameter=mycustomvalue[&$]/", $facetValueHelper->link));
            }
        }
        $this->assertEquals("mycustomvalue", $helper->get_query_parameter("mycustomparameter"));
    }
}
