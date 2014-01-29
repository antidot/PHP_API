<?php
require_once "AFS/SEARCH/afs_response_helper.php";
require_once "AFS/SEARCH/afs_replyset_helper.php";
require_once "AFS/SEARCH/afs_query.php";
require_once "AFS/SEARCH/afs_query_coder.php";

class ReplysetHelperTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleReply()
    {
        $input = json_decode('{
            "header": {
                "query": {
                    "userId": "afd070b6-4315-40cc-975d-747e28bf132a",
                    "sessionId": "5bf5642d-a262-4608-9901-45aa6e87325d",
                    "date": "2013-10-02T15:48:41+0200",
                    "queryParam": [
                        {
                            "name": "afs:service",
                            "value": "42"
                        }
                    ],
                    "mainCtx": {
                        "textQuery": "title"
                    },
                    "textQuery": "title"
                },
                "user": {
                    "requestMethod": "GET",
                    "agent": "Mozilla/5.0 (X11; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0 Iceweasel/23.0",
                    "address": "127.0.0.1",
                    "output": {
                        "format": "JSON",
                        "encoding": "gzip",
                        "charset": "UTF-8"
                    }
                },
                "performance": {
                    "durationMs": 666
                },
                "info": { }
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
                                "afs:t": "FacetTree",
                                "node": [
                                    {
                                        "key": "false",
                                        "labels": [
                                            {
                                                "label": "BAD"
                                            }
                                        ],
                                        "items": 67
                                    },
                                    {
                                        "key": "true",
                                        "labels": [
                                            {
                                                "label": "GOOD"
                                            }
                                        ],
                                        "items": 133
                                    }
                                ],
                                "layout": "TREE",
                                "type": "BOOL",
                                "id": "BOOL",
                                "labels": [
                                    {
                                        "label": "Boolean facet"
                                    }
                                ]
                            }
                        ]
                    },
                    "content": {
                        "reply": [
                            {
                                "docId": 198,
                                "uri": "http://foo.bar.baz/116",
                                "title": [
                                    {
                                        "afs:t": "KwicString",
                                            "text": "The "
                                    },
                                    {
                                        "afs:t": "KwicMatch",
                                            "match": "title"
                                    },
                                    {
                                        "afs:t": "KwicString",
                                            "text": " 116"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "viens de tomber a monté d\'un nouveau cran dans l\'étrangeté. Jamais dans l\'Histoire de l\'humanité il n\'a existé de civilisation sans enfants. Je tente d\'en imaginer les conséquences. George, qui m\'a deviné, énumère: - Comme nous ne nous reproduisons pas, la moitié féminine de l\'humanité"
                                    },
                                    {
                                        "afs:t": "KwicTruncate"
                                    }
                                ],
                                "relevance": {
                                    "rank": 3
                                },
                                "clientData": [
                                    {
                                        "contents": "<clientdata>{&quot;data&quot;: [{&quot;data1&quot;: &quot;data 0&quot;}, {&quot;data1&quot;: &quot;data 1&quot;}, {&quot;m1&quot;: &quot;m 1&quot;, &quot;m0&quot;: &quot;m 0&quot;, &quot;m3&quot;: &quot;m 3&quot;, &quot;m2&quot;: &quot;m 2&quot;}]}</clientdata>",
                                        "id": "main",
                                        "mimeType": "text/xml"
                                    }
                                ]
                            },
                            {
                                "docId": 197,
                                "uri": "http://foo.bar.baz/81",
                                "title": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "The "
                                    },
                                    {
                                        "afs:t": "KwicMatch",
                                        "match": "title"
                                    },
                                    {
                                        "afs:t": "KwicString",
                                        "text": " 81"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "morose... il n\'y a pas de quoi en être fier. J\'émets des doutes, certes; mais au fond de moi, j\'ai confiance. Ne vous en étonnez-vous pas? Il y aurait pourtant bien de quoi! Voici que va surgir de nulle part une collectivité cachée comme aucune autre. Rien de pareil, jamais, n\'est arrivé dans"
                                    },
                                    {
                                        "afs:t": "KwicTruncate"
                                    }
                                ],
                                "relevance": {
                                    "rank": 4
                                },
                                "clientData": [
                                    {
                                        "contents": "<clientdata>&lt;data&gt;&lt;data1&gt;data 0&lt;/data1&gt;&lt;data1&gt;data 1&lt;/data1&gt;&lt;multi&gt;&lt;m0&gt;m 0&lt;/m0&gt;&lt;m1&gt;m 1&lt;/m1&gt;&lt;m2&gt;m 2&lt;/m2&gt;&lt;m3&gt;m 3&lt;/m3&gt;&lt;/multi&gt;&lt;/data&gt;</clientdata>",
                                        "id": "main",
                                        "mimeType": "text/xml"
                                    }
                                ]
                            }
                        ]
                    },
                    "pager": {
                        "previousPage": 1,
                        "nextPage": 3,
                        "currentPage": 2,
                        "page": [
                            1,
                            2,
                            3,
                            4,
                            5,
                            6,
                            7,
                            8,
                            9,
                            10
                        ]
                    }
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);

        $facet_mgr = $config->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AfsFacetType::BOOL_TYPE));

        $query = new AfsQuery();
        $query = $query->set_query('title');
        $query = $query->set_replies(2);
        $query = $query->set_page(3);

        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);

        $meta = $helper->get_meta();
        $this->assertEquals('Test', $meta->get_feed());
        $this->assertEquals('200', $meta->get_total_replies());
        $this->assertEquals('42', $meta->get_duration());
        $this->assertEquals('SEARCH', $meta->get_producer());

        $this->assertTrue($helper->has_facet());
        $facets = $helper->get_facets();
        $this->assertEquals(1, count($facets));
        $this->assertEquals('Boolean facet', $facets[0]->get_label());
        $elems = $facets[0]->get_elements();
        $this->assertEquals(2, count($elems));
        // You can continue here if you want but unit tests already exists for 
        // facets.

        $this->assertEquals(2, $helper->get_nb_replies());
        $replies = $helper->get_replies();
        $this->assertEquals('The <b>title</b> 116', $replies[0]->title);
        $this->assertEquals('The <b>title</b> 81', $replies[1]->title);
        // and so on...
        
        $pager = $helper->get_pager();
        $this->assertEquals($pager->get_next()->get_page(), '3');
        $this->assertEquals($pager->get_previous()->get_page(), '1');
        // and so on...
    }

    public function testSimpleReplyWithLinks()
    {
        $input = json_decode('{
            "header": {
                "query": {
                    "userId": "afd070b6-4315-40cc-975d-747e28bf132a",
                    "sessionId": "5bf5642d-a262-4608-9901-45aa6e87325d",
                    "date": "2013-10-02T15:48:41+0200",
                    "queryParam": [
                        {
                            "name": "afs:service",
                            "value": "42"
                        }
                    ],
                    "mainCtx": {
                        "textQuery": "title"
                    },
                    "textQuery": "title"
                },
                "user": {
                    "requestMethod": "GET",
                    "agent": "Mozilla/5.0 (X11; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0 Iceweasel/23.0",
                    "address": "127.0.0.1",
                    "output": {
                        "format": "JSON",
                        "encoding": "gzip",
                        "charset": "UTF-8"
                    }
                },
                "performance": {
                    "durationMs": 666
                },
                "info": { }
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
                                "afs:t": "FacetTree",
                                "node": [
                                    {
                                        "key": "false",
                                        "labels": [
                                            {
                                                "label": "BAD"
                                            }
                                        ],
                                        "items": 67
                                    },
                                    {
                                        "key": "true",
                                        "labels": [
                                            {
                                                "label": "GOOD"
                                            }
                                        ],
                                        "items": 133
                                    }
                                ],
                                "layout": "TREE",
                                "type": "BOOL",
                                "id": "BOOL",
                                "labels": [
                                    {
                                        "label": "Boolean facet"
                                    }
                                ]
                            }
                        ]
                    },
                    "content": {
                        "reply": [
                            {
                                "docId": 198,
                                "uri": "http://foo.bar.baz/116",
                                "title": [
                                    {
                                        "afs:t": "KwicString",
                                            "text": "The "
                                    },
                                    {
                                        "afs:t": "KwicMatch",
                                            "match": "title"
                                    },
                                    {
                                        "afs:t": "KwicString",
                                            "text": " 116"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "viens de tomber a monté d\'un nouveau cran dans l\'étrangeté. Jamais dans l\'Histoire de l\'humanité il n\'a existé de civilisation sans enfants. Je tente d\'en imaginer les conséquences. George, qui m\'a deviné, énumère: - Comme nous ne nous reproduisons pas, la moitié féminine de l\'humanité"
                                    },
                                    {
                                        "afs:t": "KwicTruncate"
                                    }
                                ],
                                "relevance": {
                                    "rank": 3
                                },
                                "clientData": [
                                    {
                                        "contents": "<clientdata>{&quot;data&quot;: [{&quot;data1&quot;: &quot;data 0&quot;}, {&quot;data1&quot;: &quot;data 1&quot;}, {&quot;m1&quot;: &quot;m 1&quot;, &quot;m0&quot;: &quot;m 0&quot;, &quot;m3&quot;: &quot;m 3&quot;, &quot;m2&quot;: &quot;m 2&quot;}]}</clientdata>",
                                        "id": "main",
                                        "mimeType": "text/xml"
                                    }
                                ]
                            },
                            {
                                "docId": 197,
                                "uri": "http://foo.bar.baz/81",
                                "title": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "The "
                                    },
                                    {
                                        "afs:t": "KwicMatch",
                                        "match": "title"
                                    },
                                    {
                                        "afs:t": "KwicString",
                                        "text": " 81"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "morose... il n\'y a pas de quoi en être fier. J\'émets des doutes, certes; mais au fond de moi, j\'ai confiance. Ne vous en étonnez-vous pas? Il y aurait pourtant bien de quoi! Voici que va surgir de nulle part une collectivité cachée comme aucune autre. Rien de pareil, jamais, n\'est arrivé dans"
                                    },
                                    {
                                        "afs:t": "KwicTruncate"
                                    }
                                ],
                                "relevance": {
                                    "rank": 4
                                },
                                "clientData": [
                                    {
                                        "contents": "<clientdata>&lt;data&gt;&lt;data1&gt;data 0&lt;/data1&gt;&lt;data1&gt;data 1&lt;/data1&gt;&lt;multi&gt;&lt;m0&gt;m 0&lt;/m0&gt;&lt;m1&gt;m 1&lt;/m1&gt;&lt;m2&gt;m 2&lt;/m2&gt;&lt;m3&gt;m 3&lt;/m3&gt;&lt;/multi&gt;&lt;/data&gt;</clientdata>",
                                        "id": "main",
                                        "mimeType": "text/xml"
                                    }
                                ]
                            }
                        ]
                    },
                    "pager": {
                        "previousPage": 1,
                        "nextPage": 3,
                        "currentPage": 2,
                        "page": [
                            1,
                            2,
                            3,
                            4,
                            5,
                            6,
                            7,
                            8,
                            9,
                            10
                        ]
                    }
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);

        $coder = new AfsQueryCoder('foo.php');
        $config->set_query_coder($coder);
        $facet_mgr = $config->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AfsFacetType::BOOL_TYPE));

        $query = new AfsQuery();
        $query = $query->set_query('title');
        $query = $query->set_replies(2);
        $query = $query->set_page(3);

        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);

        $meta = $helper->get_meta();
        $this->assertEquals('Test', $meta->get_feed());
        $this->assertEquals('200', $meta->get_total_replies());
        $this->assertEquals('42', $meta->get_duration());
        $this->assertEquals('SEARCH', $meta->get_producer());

        $this->assertTrue($helper->has_facet());
        $facets = $helper->get_facets();
        $this->assertEquals(1, count($facets));
        $this->assertEquals('Boolean facet', $facets[0]->get_label());
        $elems = $facets[0]->get_elements();
        $this->assertEquals(2, count($elems));
        $this->assertEquals('BAD', $elems[0]->label);
        $this->assertTrue($elems[0]->link != null);
        $this->assertEquals($elems[0]->link, 'foo.php?replies=2&query=title&filter=BOOL_false');
        // You can continue here if you want but unit tests already exists for 
        // facets.

        $this->assertEquals(2, $helper->get_nb_replies());
        $replies = $helper->get_replies();
        $this->assertEquals('The <b>title</b> 116', $replies[0]->title);
        $this->assertEquals('The <b>title</b> 81', $replies[1]->title);
        // and so on...
        
        $pager = $helper->get_pager();
        $this->assertEquals($pager->get_next(), 'foo.php?replies=2&page=3&query=title');
        $this->assertEquals($pager->get_previous(), 'foo.php?replies=2&query=title');
        // and so on...
    }

    public function testFormattedReplyWithLinks()
    {
        $input = json_decode('{
            "header": {
                "query": {
                    "userId": "afd070b6-4315-40cc-975d-747e28bf132a",
                    "sessionId": "5bf5642d-a262-4608-9901-45aa6e87325d",
                    "date": "2013-10-02T15:48:41+0200",
                    "queryParam": [
                        {
                            "name": "afs:service",
                            "value": "42"
                        }
                    ],
                    "mainCtx": {
                        "textQuery": "title"
                    },
                    "textQuery": "title"
                },
                "user": {
                    "requestMethod": "GET",
                    "agent": "Mozilla/5.0 (X11; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0 Iceweasel/23.0",
                    "address": "127.0.0.1",
                    "output": {
                        "format": "JSON",
                        "encoding": "gzip",
                        "charset": "UTF-8"
                    }
                },
                "performance": {
                    "durationMs": 666
                },
                "info": { }
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
                                "afs:t": "FacetTree",
                                "node": [
                                    {
                                        "key": "false",
                                        "labels": [
                                            {
                                                "label": "BAD"
                                            }
                                        ],
                                        "items": 67
                                    },
                                    {
                                        "key": "true",
                                        "labels": [
                                            {
                                                "label": "GOOD"
                                            }
                                        ],
                                        "items": 133
                                    }
                                ],
                                "layout": "TREE",
                                "type": "BOOL",
                                "id": "BOOL",
                                "labels": [
                                    {
                                        "label": "Boolean facet"
                                    }
                                ]
                            }
                        ]
                    },
                    "content": {
                        "reply": [
                            {
                                "docId": 198,
                                "uri": "http://foo.bar.baz/116",
                                "title": [
                                    {
                                        "afs:t": "KwicString",
                                            "text": "The "
                                    },
                                    {
                                        "afs:t": "KwicMatch",
                                            "match": "title"
                                    },
                                    {
                                        "afs:t": "KwicString",
                                            "text": " 116"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "viens de tomber a monté d\'un nouveau cran dans l\'étrangeté. Jamais dans l\'Histoire de l\'humanité il n\'a existé de civilisation sans enfants. Je tente d\'en imaginer les conséquences. George, qui m\'a deviné, énumère: - Comme nous ne nous reproduisons pas, la moitié féminine de l\'humanité"
                                    },
                                    {
                                        "afs:t": "KwicTruncate"
                                    }
                                ],
                                "relevance": {
                                    "rank": 3
                                },
                                "clientData": [
                                    {
                                        "contents": "<clientdata>{&quot;data&quot;: [{&quot;data1&quot;: &quot;data 0&quot;}, {&quot;data1&quot;: &quot;data 1&quot;}, {&quot;m1&quot;: &quot;m 1&quot;, &quot;m0&quot;: &quot;m 0&quot;, &quot;m3&quot;: &quot;m 3&quot;, &quot;m2&quot;: &quot;m 2&quot;}]}</clientdata>",
                                        "id": "main",
                                        "mimeType": "text/xml"
                                    }
                                ]
                            },
                            {
                                "docId": 197,
                                "uri": "http://foo.bar.baz/81",
                                "title": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "The "
                                    },
                                    {
                                        "afs:t": "KwicMatch",
                                        "match": "title"
                                    },
                                    {
                                        "afs:t": "KwicString",
                                        "text": " 81"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "morose... il n\'y a pas de quoi en être fier. J\'émets des doutes, certes; mais au fond de moi, j\'ai confiance. Ne vous en étonnez-vous pas? Il y aurait pourtant bien de quoi! Voici que va surgir de nulle part une collectivité cachée comme aucune autre. Rien de pareil, jamais, n\'est arrivé dans"
                                    },
                                    {
                                        "afs:t": "KwicTruncate"
                                    }
                                ],
                                "relevance": {
                                    "rank": 4
                                },
                                "clientData": [
                                    {
                                        "contents": "<clientdata>&lt;data&gt;&lt;data1&gt;data 0&lt;/data1&gt;&lt;data1&gt;data 1&lt;/data1&gt;&lt;multi&gt;&lt;m0&gt;m 0&lt;/m0&gt;&lt;m1&gt;m 1&lt;/m1&gt;&lt;m2&gt;m 2&lt;/m2&gt;&lt;m3&gt;m 3&lt;/m3&gt;&lt;/multi&gt;&lt;/data&gt;</clientdata>",
                                        "id": "main",
                                        "mimeType": "text/xml"
                                    }
                                ]
                            }
                        ]
                    },
                    "pager": {
                        "previousPage": 1,
                        "nextPage": 3,
                        "currentPage": 2,
                        "page": [
                            1,
                            2,
                            3,
                            4,
                            5,
                            6,
                            7,
                            8,
                            9,
                            10
                        ]
                    }
                }
            ]
        }');

        $config = new AfsHelperConfiguration();

        $coder = new AfsQueryCoder('foo.php');
        $config->set_query_coder($coder);
        $facet_mgr = $config->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AfsFacetType::BOOL_TYPE));

        $query = new AfsQuery();
        $query = $query->set_query('title');
        $query = $query->set_replies(2);
        $query = $query->set_page(3);

        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);

        $meta = $helper->get_meta();
        $this->assertEquals('Test', $meta['feed']);
        $this->assertEquals('200', $meta['total_replies']);
        $this->assertEquals('42', $meta['duration']);
        $this->assertEquals('SEARCH', $meta['producer']);

        $this->assertTrue($helper->has_facet());
        $facets = $helper->get_facets();
        $this->assertEquals(1, count($facets));
        $this->assertEquals('Boolean facet', $facets[0]['label']);
        $elems = $facets[0]['values'];
        $this->assertEquals(2, count($elems));
        $this->assertEquals('BAD', $elems[0]['label']);
        $this->assertTrue($elems[0]['link'] != null);
        $this->assertEquals($elems[0]['link'], 'foo.php?replies=2&query=title&filter=BOOL_false');
        // You can continue here if you want but unit tests already exists for 
        // facets.

        $this->assertEquals(2, $helper->get_nb_replies());
        $replies = $helper->get_replies();
        $this->assertEquals('The <b>title</b> 116', $replies[0]['title']);
        $this->assertEquals('The <b>title</b> 81', $replies[1]['title']);
        // and so on...
        
        $pager = $helper->get_pager();
        $this->assertEquals($pager['pages']['next'], 'foo.php?replies=2&page=3&query=title');
        $this->assertEquals($pager['pages']['previous'], 'foo.php?replies=2&query=title');
        // and so on...
    }
}


