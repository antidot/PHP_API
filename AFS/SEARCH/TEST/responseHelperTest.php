<?php ob_start();
require_once "AFS/SEARCH/afs_response_helper.php";
require_once "AFS/SEARCH/afs_query.php";
require_once "AFS/SEARCH/afs_producer.php";

class ResponseHelperTest extends PHPUnit_Framework_TestCase
{
    public function testNoReplySet()
    {
        $input = json_decode('{
                "header": {
                    "query": {
                        "userId": "foo",
                        "sessionId": "bar"
                    },
                    "user": { },
                    "performance": {
                        "durationMs": 215
                    },
                    "info": { }
                }
            }');

        $config = new AfsHelperConfiguration();
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $query, $config);
        $this->assertFalse($response->has_replyset());
        $this->assertFalse($response->has_replyset('Catalog'));
        $this->assertFalse($response->in_error());
        $this->assertFalse($response->has_spellcheck());
        $this->assertFalse($response->has_promote());
        $this->assertFalse($response->has_concept());

        try {
            $response->get_replysets();
            $this->fail('Should have raised exception');
        } catch (AfsNoReplyException $e) { }
        try {
            $response->get_replyset('FOO');
            $this->fail('Should have raised exception');
        } catch (AfsNoReplyException $e) { }
        try {
            $response->get_spellchecks();
            $this->fail('Should have raised exception');
        } catch (AfsNoReplyException $e) { }
        try {
            $response->get_spellchecks('FOO');
            $this->fail('Should have raised exception');
        } catch (AfsNoReplyException $e) { }
        try {
            $response->get_promotes();
            $this->fail('Should have raised exception');
        } catch (AfsNoReplyException $e) { }
        try {
            $response->get_concepts();
            $this->fail('Should have raised exception');
        } catch (AfsNoReplyException $e) { }
        try {
            $response->get_concept('FOO');
            $this->fail('Should have raised exception');
        } catch (AfsNoReplyException $e) { }
    }

    public function testReplysetWith()
    {
        $input = json_decode('{
                "header": {
                    "query": {
                        "userId": "foo",
                        "sessionId": "bar"
                    },
                    "user": { },
                    "performance": {
                        "durationMs": 211
                    },
                    "info": { }
                },
                "replySet": [
                    {
                        "meta": {
                            "uri": "Catalog",
                            "totalItems": 5,
                            "totalItemsIsExact": true,
                            "pageItems": 5,
                            "firstPageItem": 1,
                            "lastPageItem": 5,
                            "durationMs": 4,
                            "firstPaFId": 1,
                            "lastPaFId": 1,
                            "producer": "SEARCH"
                        }
                    }
                ]
            }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $query, $config);
        $this->assertEquals(211, $response->get_duration());
        $this->assertTrue($response->has_replyset());
        $this->assertEquals('Catalog', $response->get_replyset()->get_meta()->get_feed());
        $this->assertFalse($response->get_replyset()->has_reply());
    }

    public function testRetrieveAppropriateReplyset()
    {
        $input = json_decode('{
                "header": {
                    "query": {
                        "userId": "foo",
                        "sessionId": "bar"
                    },
                    "user": { },
                    "performance": {
                        "durationMs": 211
                    },
                    "info": { }
                },
                "replySet": [
                    {
                        "meta": {
                            "uri": "Country",
                            "totalItems": 5,
                            "totalItemsIsExact": true,
                            "pageItems": 5,
                            "firstPageItem": 1,
                            "lastPageItem": 5,
                            "durationMs": 4,
                            "firstPaFId": 1,
                            "lastPaFId": 1,
                            "producer": "SEARCH"
                        }
                    },
                    {
                        "meta": {
                            "uri": "Catalog",
                            "totalItems": 666,
                            "totalItemsIsExact": true,
                            "pageItems": 5,
                            "firstPageItem": 1,
                            "lastPageItem": 5,
                            "durationMs": 4,
                            "firstPaFId": 1,
                            "lastPaFId": 1,
                            "producer": "SPELLCHECK"
                        },
                        "content": {
                            "reply": [
                                {
                                    "uri": "Catalog",
                                    "suggestion": [
                                        {
                                            "items": [
                                                {
                                                    "match": {
                                                        "text": "FOO"
                                                    }
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }
                    },
                    {
                        "meta": {
                            "uri": "Catalog",
                            "totalItems": 42,
                            "totalItemsIsExact": true,
                            "pageItems": 5,
                            "firstPageItem": 1,
                            "lastPageItem": 5,
                            "durationMs": 4,
                            "firstPaFId": 1,
                            "lastPaFId": 1,
                            "producer": "SEARCH"
                        }
                    }
                ]
            }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $query, $config);
        $this->assertTrue($response->has_replyset());
        $replyset = $response->get_replyset('Catalog');
        $this->assertEquals('Catalog', $replyset->get_meta()->get_feed());
        $this->assertEquals(AfsProducer::SEARCH, $replyset->get_meta()->get_producer());
        $this->assertEquals(42, $replyset->get_meta()->get_total_replies());

        $this->assertTrue($response->has_spellcheck());
    }

    public function testRetrieveUnreachableReplyset()
    {
        $input = json_decode('{
                "header": {
                    "query": {
                        "userId": "foo",
                        "sessionId": "bar"
                    },
                    "user": { },
                    "performance": {
                        "durationMs": 211
                    },
                    "info": { }
                },
                "replySet": [
                    {
                        "meta": {
                            "uri": "Country",
                            "totalItems": 5,
                            "totalItemsIsExact": true,
                            "pageItems": 5,
                            "firstPageItem": 1,
                            "lastPageItem": 5,
                            "durationMs": 4,
                            "firstPaFId": 1,
                            "lastPaFId": 1,
                            "producer": "SEARCH"
                        }
                    },
                    {
                        "meta": {
                            "uri": "Catalog",
                            "totalItems": 666,
                            "totalItemsIsExact": true,
                            "pageItems": 5,
                            "firstPageItem": 1,
                            "lastPageItem": 5,
                            "durationMs": 4,
                            "firstPaFId": 1,
                            "lastPaFId": 1,
                            "producer": "SPELLCHECK"
                        },
                        "content": {
                            "reply": [
                                {
                                    "uri": "Catalog",
                                    "suggestion": [
                                        {
                                            "items": [
                                                {
                                                    "match": {
                                                        "text": "FOO"
                                                    }
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }
                    }
                ]
            }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $query, $config);
        $this->assertTrue($response->has_replyset());
        try {
            $replyset = $response->get_replyset('Catalog');
            $this->fail('Should not have found any appropriate reply set');
        } catch (OutOfBoundsException $e) { }
    }

    public function testRetrieveConcept()
    {
        $input = json_decode('{
                "header": {
                    "query": {
                        "userId": "foo",
                        "sessionId": "bar"
                    },
                    "user": { },
                    "performance": {
                        "durationMs": 211
                    },
                    "info": { }
                },
                "replySet": [
                    {
                      "meta": {
                          "uri": "Default",
                          "totalItems": 1,
                          "totalItemsIsExact": true,
                          "pageItems": 1,
                          "firstPageItem": 1,
                          "lastPageItem": 1,
                          "durationMs": 0,
                          "firstPaFId": 1,
                          "lastPaFId": 1,
                          "producer": "CONCEPT"
                      },
                      "content": {
                          "reply": [
                              {
                                  "docId": 1,
                                  "uri": "concept",
                                  "concept": {
                                      "query": {
                                          "items": [
                                              {
                                                  "afs:t": "QueryMatch",
                                                  "text": "mariage",
                                                  "uri": [ "lnf:taxo#QI-thm2009862" ]
                                              }
                                          ]
                                      },
                                      "concepts": {
                                          "concept": [
                                              {
                                                  "uri": "lnf:taxo#QI-thm2009862",
                                                  "contents": "foo"
                                              }
                                          ]
                                      }
                                  }
                              }
                          ]
                      }
                  }
                ]
            }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $query, $config);
        
        $this->assertTrue($response->has_concept());
        $concept_helper = $response->get_concept('Default');
        $concept_items = $concept_helper->get_items();
        $this->assertEquals(1, count($concept_items));
        $concept_item = each($concept_items);
        $concept_item = $concept_item['value'];
        $this->assertEquals('mariage', $concept_item->get_text());
        $data = $concept_item->get_data();
        $item = each($data);
        $this->assertEquals('foo', $item['value']);
        $this->assertEquals('lnf:taxo#QI-thm2009862', $item['key']);
    }

    public function testArraysFormat()
    {
        $input = json_decode('{
                "header": {
                    "query": {
                        "userId": "foo",
                        "sessionId": "bar"
                    },
                    "user": { },
                    "performance": {
                        "durationMs": 211
                    },
                    "info": { }
                },
                "replySet": [
                    {
                        "meta": {
                            "uri": "Country",
                            "totalItems": 5,
                            "totalItemsIsExact": true,
                            "pageItems": 5,
                            "firstPageItem": 1,
                            "lastPageItem": 5,
                            "durationMs": 4,
                            "firstPaFId": 1,
                            "lastPaFId": 1,
                            "producer": "SEARCH"
                        }
                    },
                    {
                        "meta": {
                            "uri": "Catalog",
                            "totalItems": 666,
                            "totalItemsIsExact": true,
                            "pageItems": 5,
                            "firstPageItem": 1,
                            "lastPageItem": 5,
                            "durationMs": 4,
                            "firstPaFId": 1,
                            "lastPaFId": 1,
                            "producer": "SPELLCHECK"
                        },
                        "content": {
                            "reply": [
                                {
                                    "uri": "Catalog",
                                    "suggestion": [ { "items": [ { "match": { "text": "FOO" } } ] } ]
                                }
                            ]
                        }
                    },
                    {
                        "meta": {
                            "uri": "Catalog",
                            "totalItems": 42,
                            "totalItemsIsExact": true,
                            "pageItems": 5,
                            "firstPageItem": 1,
                            "lastPageItem": 5,
                            "durationMs": 4,
                            "firstPaFId": 1,
                            "lastPaFId": 1,
                            "producer": "SEARCH"
                        }
                    }
                ]
            }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::ARRAYS);
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $query, $config);
        $response = $response->format();

        $this->assertTrue(array_key_exists('replysets', $response));
        $replyset = $response['replysets']['Catalog'];
        $this->assertEquals('Catalog', $replyset['meta']['feed']);

        $this->assertTrue(array_key_exists('spellchecks', $response));
        $spellcheck = $response['spellchecks'];
        $this->assertTrue(array_key_exists('Catalog', $spellcheck));
        $res = $spellcheck['Catalog'];
        $this->assertEquals(1, count($res));
        $this->assertEquals('FOO', $res[0]['raw']);
    }

    public function testFromParameterIsSetForHelpers()
    {
        $input = json_decode('{
            "header": {
                "query": {
                    "userId": "afd070b6-4315-40cc-975d-747e28bf132a",
                    "sessionId": "5bf5642d-a262-4608-9901-45aa6e87325d"
                },
                "performance": {
                    "durationMs": 666
                }
            },
            "replySet": [
                {
                    "meta": {
                        "uri": "Test",
                        "totalItems": 200,
                        "totalItemsIsExact": true,
                        "pageItems": 2,
                        "firstPageItem": 3,
                        "lastPageItem": 4,
                        "durationMs": 42,
                        "firstPaFId": 1,
                        "lastPaFId": 1,
                        "producer": "SEARCH"
                    },
                    "facets": {
                        "facet": [
                            {
                                "afs:t": "FacetInterval",
                                "interval": [
                                    {
                                        "key": "[0 .. 3]",
                                        "items": 1
                                    }
                                ],
                                "layout": "INTERVAL",
                                "type": "REAL",
                                "id": "Foo"
                            }
                        ]
                    }
                },
                {
                    "meta": {
                        "uri": "Catalog",
                        "totalItems": 2,
                        "totalItemsIsExact": true,
                        "pageItems": 2,
                        "firstPageItem": 1,
                        "lastPageItem": 1,
                        "durationMs": 4,
                        "firstPaFId": 1,
                        "lastPaFId": 1,
                        "producer": "SPELLCHECK"
                    },
                    "content": {
                        "reply": [
                            {
                                "docId": 1,
                                "uri": "Catalog",
                                "title": [
                                    {
                                        "afs:t": "KwicMatch",
                                        "match": "LIGNE"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "LIGNE"
                                    }
                                ],
                                "suggestion": [ { "items": [ { "match": { "text": "LIGNE" } } ] } ]
                            }
                        ]
                    }
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $query, $config);

        $this->assertTrue($response->has_spellcheck());
        $spellchecks = $response->get_spellchecks();
        $this->assertFalse(empty($spellchecks));
        $spellcheck_helper = $spellchecks['Catalog'][0];
        $query = $spellcheck_helper->get_query();
        $this->assertEquals(AfsOrigin::SPELLCHECK, $query->get_from());

        $this->assertTrue($response->has_replyset());
        $replysets = $response->get_replysets();
        $replyset_helper = $replysets['Test'];
        $this->assertTrue($replyset_helper->has_facet());
        $facets = $replyset_helper->get_facets();
        $this->assertFalse(empty($facets));
        $elements = $facets[0]->get_elements();
        $this->assertFalse(empty($elements));
        $this->assertEquals(AfsOrigin::FACET, $elements[0]->query->get_from());

    }

    public function testIntrospectionResponseShouldInitializeMetadata() {
        $input = json_decode('{
            "header": {
                "query": {
                    "userId": "afd070b6-4315-40cc-975d-747e28bf132a",
                    "sessionId": "5bf5642d-a262-4608-9901-45aa6e87325d"
                },
                "performance": {
                    "durationMs": 666
                }
            },
            "metadata": [
            {
              "uri": "Catalog",
              "meta": {
                "producer": [],
                "info": {
                  "sizeKb": 409093,
                  "date": 1447110000,
                  "searchFeedInfo": {
                    "nbDocs": 33210,
                    "nbShards": 1,
                    "setInfos": [
                      {
                        "setId": "Antidot_Root_Field",
                        "childrenInfos": [],
                        "facetInfos": [
                          {
                            "afs:t": "FacetTree",
                            "layout": "TREE",
                            "type": "INTEGER",
                            "id": "product_id",
                            "sticky": false,
                            "filter": true
                          },
                          {
                            "afs:t": "FacetTree",
                            "layout": "TREE",
                            "type": "STRING",
                            "id": "name",
                            "sticky": false,
                            "filter": true
                          },
                          {
                            "afs:t": "FacetTree",
                            "layout": "TREE",
                            "type": "REAL",
                            "id": "price_from",
                            "sticky": false,
                            "filter": true
                          }
                        ]
                      }
                    ]
                  }
                }
              }
            }
          ]
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $response_helper = new AfsResponseHelper($input, new AfsQuery(), $config);

        $this->assertNotEmpty($response_helper->get_all_metadata());
        $this->assertTrue($response_helper->has_metadata('Catalog'));
        $this->assertNotNull($response_helper->get_feed_metadata('Catalog'));
    }
}


