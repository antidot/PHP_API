<?php
require_once "afs_response_helper.php";

class ResponseHelperTest extends PHPUnit_Framework_TestCase
{
    public function testNoReplySet()
    {
        $input = json_decode('{
                "header": {
                    "query": { },
                    "user": { },
                    "performance": {
                        "durationMs": 215
                    },
                    "info": { }
                }
            }');

        $facet_mgr = new AfsFacetManager();
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $facet_mgr, $query);
        $this->assertFalse($response->has_replyset());
    }

    public function testReplysetWith()
    {
        $input = json_decode('{
                "header": {
                    "query": { },
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

        $facet_mgr = new AfsFacetManager();
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $facet_mgr, $query, null, AFS_HELPER_FORMAT);
        $this->assertEquals(211, $response->get_duration());
        $this->assertTrue($response->has_replyset());
        $this->assertEquals('Catalog', $response->get_replyset()->get_meta()->get_feed());
        $this->assertFalse($response->get_replyset()->has_reply());
    }

    public function testRetrieveAppropriateReplyset()
    {
        $input = json_decode('{
                "header": {
                    "query": { },
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

        $facet_mgr = new AfsFacetManager();
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $facet_mgr, $query, null, AFS_HELPER_FORMAT);
        $this->assertTrue($response->has_replyset());
        $replyset = $response->get_replyset('Catalog');
        $this->assertEquals('Catalog', $replyset->get_meta()->get_feed());
        $this->assertEquals(AFS_PRODUCER_SEARCH, $replyset->get_meta()->get_producer());
        $this->assertEquals(42, $replyset->get_meta()->get_total_replies());
    }

    public function testRetrieveUnreachableReplyset()
    {
        $input = json_decode('{
                "header": {
                    "query": { },
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

        $facet_mgr = new AfsFacetManager();
        $query = new AfsQuery();
        $response = new AfsResponseHelper($input, $facet_mgr, $query, null, AFS_HELPER_FORMAT);
        $this->assertTrue($response->has_replyset());
        try {
            $replyset = $response->get_replyset('Catalog');
            $this->fail('Should not have found any appropriate reply set');
        } catch (OutOfBoundsException $e) { }
    }
}

?>
