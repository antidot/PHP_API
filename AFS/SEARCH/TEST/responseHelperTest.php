<?php
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
        $item = each($concept_item->get_data());
        $this->assertEquals('foo', $item['value']);
        $this->assertEquals('lnf:taxo#QI-thm2009862', $item['key']);
    }
}


