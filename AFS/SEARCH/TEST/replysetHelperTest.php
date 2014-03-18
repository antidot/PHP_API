<?php ob_start();
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

        $query = new AfsQuery();
        $query = $query->set_query('title');
        $query = $query->set_replies(2);
        $query = $query->set_page(3);

        $config = new AfsHelperConfiguration();
        $facet_mgr = $query->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AfsFacetType::BOOL_TYPE));

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

        $this->assertTrue($helper->has_pager());
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

        $query = new AfsQuery();
        $query = $query->set_query('title');
        $query = $query->set_replies(2);
        $query = $query->set_page(3);

        $config = new AfsHelperConfiguration();
        $coder = new AfsQueryCoder('foo.php');
        $config->set_query_coder($coder);
        $facet_mgr = $query->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AfsFacetType::BOOL_TYPE));

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

        $this->assertTrue($helper->has_pager());
        $pager = $helper->get_pager();
        $this->assertEquals('foo.php?replies=2&query=title&page=3', $pager->get_next());
        $this->assertEquals('foo.php?replies=2&query=title', $pager->get_previous());
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

        $query = new AfsQuery();
        $query = $query->set_query('title');
        $query = $query->set_replies(2);
        $query = $query->set_page(3);

        $config = new AfsHelperConfiguration();

        $coder = new AfsQueryCoder('foo.php');
        $config->set_query_coder($coder);
        $facet_mgr = $query->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AfsFacetType::BOOL_TYPE));

        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);

        $meta = $helper->get_meta()->format();
        $this->assertEquals('Test', $meta['feed']);
        $this->assertEquals('200', $meta['total_replies']);
        $this->assertEquals('42', $meta['duration']);
        $this->assertEquals('SEARCH', $meta['producer']);

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
        $this->assertEquals('The <b>title</b> 116', $replies[0]->get_title());
        $this->assertEquals('The <b>title</b> 81', $replies[1]->get_title());
        // and so on...

        $this->assertTrue($helper->has_pager());
        $pager = $helper->get_pager()->format();
        $this->assertEquals('foo.php?replies=2&query=title&page=3', $pager['pages']['next']);
        $this->assertEquals('foo.php?replies=2&query=title', $pager['pages']['previous']);
        // and so on...
    }

    public function testSimpleReplyWithoutPagerWithoutFacet()
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
                            }
                        ]
                    }
                }
            ]
        }');

        $query = new AfsQuery();
        $query = $query->set_query('title');
        $query = $query->set_replies(1);
        $query = $query->set_page(3);

        $config = new AfsHelperConfiguration();
        $facet_mgr = $query->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AfsFacetType::BOOL_TYPE));

        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);

        $meta = $helper->get_meta();
        $this->assertEquals('Test', $meta->get_feed());
        $this->assertEquals('200', $meta->get_total_replies());
        $this->assertEquals('42', $meta->get_duration());
        $this->assertEquals('SEARCH', $meta->get_producer());

        $this->assertFalse($helper->has_facet());

        $this->assertEquals(1, $helper->get_nb_replies());
        $replies = $helper->get_replies();
        $this->assertEquals('The <b>title</b> 116', $replies[0]->title);
        // and so on...

        $this->assertFalse($helper->has_pager());
    }

    public function testRetrieveFacetFromReplyWhereasItAsNotBeenFullyDeclared()
    {
        $input = json_decode('{
            "header": {
                "query": {
                    "userId": "afd070b6-4315-40cc-975d-747e28bf132a",
                    "sessionId": "5bf5642d-a262-4608-9901-45aa6e87325d",
                    "date": "2013-10-02T15:48:41+0200",
                    "queryParam": [],
                    "mainCtx": { },
                    "textQuery": "title"
                },
                "user": { },
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
                                        "text": "Foo"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "Bar"
                                    }
                                ],
                                "relevance": {
                                    "rank": 3
                                }
                            }
                        ]
                    }
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $query = new AfsQuery();
        $facet_mgr = $query->get_facet_manager();
        $facet_mgr->set_facet_order(array('BOOL'), AfsFacetSort::STRICT);
        $this->assertTrue($facet_mgr->has_facet('BOOL'));
        $this->assertEquals(AfsFacetType::UNKNOWN_TYPE, $facet_mgr->get_facet('BOOL')->get_type());

        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);
        $this->assertEquals(AfsFacetType::BOOL_TYPE, $facet_mgr->get_facet('BOOL')->get_type());
    }

    public function testRetrieveIntervalFacetFromReplyWhereasItAsNotBeenFullyDeclared()
    {
        $input = json_decode('{
            "header": {
                "query": {
                    "userId": "afd070b6-4315-40cc-975d-747e28bf132a",
                    "sessionId": "5bf5642d-a262-4608-9901-45aa6e87325d",
                    "date": "2013-10-02T15:48:41+0200",
                    "queryParam": [],
                    "mainCtx": { },
                    "textQuery": "title"
                },
                "user": { },
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
                                "afs:t": "FacetInterval",
                                "interval": [
                                    {
                                        "key": "[0 .. 3]",
                                        "items": 1
                                    },
                                    {
                                        "key": "[3 .. 6]",
                                        "items": 1
                                    }
                                ],
                                "layout": "INTERVAL",
                                "type": "REAL",
                                "id": "Foo",
                                "labels": [
                                    {
                                        "label": "Real facet"
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
                                        "text": "Foo"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "Bar"
                                    }
                                ],
                                "relevance": {
                                    "rank": 3
                                }
                            }
                        ]
                    }
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $query = new AfsQuery();
        $facet_mgr = $query->get_facet_manager();
        $facet_mgr->set_facet_order(array('Foo'), AfsFacetSort::STRICT);
        $this->assertTrue($facet_mgr->has_facet('Foo'));
        $this->assertEquals(AfsFacetType::UNKNOWN_TYPE, $facet_mgr->get_facet('Foo')->get_type());

        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);
        $this->assertEquals(AfsFacetType::REAL_TYPE, $facet_mgr->get_facet('Foo')->get_type());
    }

    public function testLazyFacetOrderingAllFacetsDeclared()
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
                                        "key": "[3 .. 6]",
                                        "items": 1
                                    }
                                ],
                                "layout": "INTERVAL",
                                "type": "INTEGER",
                                "id": "Bar"
                            },
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
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $query = new AfsQuery();
        $facet_mgr = $query->get_facet_manager();
        $facet_mgr->set_facet_order(array('Foo', 'Bar'), AfsFacetSort::LAX);
        $this->assertTrue($facet_mgr->has_facet('Foo'));
        $this->assertTrue($facet_mgr->has_facet('Bar'));

        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);
        $this->assertEquals(AfsFacetType::REAL_TYPE, $facet_mgr->get_facet('Foo')->get_type());
        $this->assertEquals(AfsFacetType::INTEGER_TYPE, $facet_mgr->get_facet('Bar')->get_type());

        $facets = $helper->get_facets();
        $this->assertEquals(2, count($facets));
        $this->assertEquals('Foo', $facets[0]->get_id());
        $this->assertEquals('Bar', $facets[1]->get_id());
    }

    public function testLazyFacetOrderingSomeFacetsDeclared()
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
                                        "key": "[\"3\" .. \"6\"]",
                                        "items": 1
                                    }
                                ],
                                "layout": "INTERVAL",
                                "type": "STRING",
                                "id": "Bat"
                            },
                            {
                                "afs:t": "FacetInterval",
                                "interval": [
                                    {
                                        "key": "[3 .. 6]",
                                        "items": 1
                                    }
                                ],
                                "layout": "INTERVAL",
                                "type": "DATE",
                                "id": "Baz"
                            },
                            {
                                "afs:t": "FacetInterval",
                                "interval": [
                                    {
                                        "key": "[3 .. 6]",
                                        "items": 1
                                    }
                                ],
                                "layout": "INTERVAL",
                                "type": "INTEGER",
                                "id": "Bar"
                            },
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
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $query = new AfsQuery();
        $facet_mgr = $query->get_facet_manager();
        $facet_mgr->set_facet_order(array('Foo', 'Bar'), AfsFacetSort::LAX);
        $this->assertTrue($facet_mgr->has_facet('Foo'));
        $this->assertTrue($facet_mgr->has_facet('Bar'));
        $this->assertFalse($facet_mgr->has_facet('Baz'));
        $this->assertFalse($facet_mgr->has_facet('Bat'));

        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);
        $this->assertEquals(AfsFacetType::REAL_TYPE, $facet_mgr->get_facet('Foo')->get_type());
        $this->assertEquals(AfsFacetType::INTEGER_TYPE, $facet_mgr->get_facet('Bar')->get_type());

        $facets = $helper->get_facets();
        $this->assertEquals(4, count($facets));
        $this->assertEquals('Foo', $facets[0]->get_id());
        $this->assertEquals('Bar', $facets[1]->get_id());
        $this->assertEquals('Bat', $facets[2]->get_id());
        $this->assertEquals('Baz', $facets[3]->get_id());
    }

    public function testLazyFacetOrderingMoreFacetsDeclaredThanPresentInReply()
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
                                        "key": "[3 .. 6]",
                                        "items": 1
                                    }
                                ],
                                "layout": "INTERVAL",
                                "type": "DATE",
                                "id": "Baz"
                            },
                            {
                                "afs:t": "FacetInterval",
                                "interval": [
                                    {
                                        "key": "[3 .. 6]",
                                        "items": 1
                                    }
                                ],
                                "layout": "INTERVAL",
                                "type": "INTEGER",
                                "id": "Bar"
                            },
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
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $query = new AfsQuery();
        $facet_mgr = $query->get_facet_manager();
        $facet_mgr->set_facet_order(array('Foo', 'Bal', 'Bar'), AfsFacetSort::LAX);
        $this->assertTrue($facet_mgr->has_facet('Foo'));
        $this->assertTrue($facet_mgr->has_facet('Bal'));
        $this->assertTrue($facet_mgr->has_facet('Bar'));
        $this->assertFalse($facet_mgr->has_facet('Baz'));

        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);
        $this->assertEquals(AfsFacetType::REAL_TYPE, $facet_mgr->get_facet('Foo')->get_type());
        $this->assertEquals(AfsFacetType::INTEGER_TYPE, $facet_mgr->get_facet('Bar')->get_type());

        $facets = $helper->get_facets();
        $this->assertEquals(3, count($facets));
        $this->assertEquals('Foo', $facets[0]->get_id());
        $this->assertEquals('Bar', $facets[1]->get_id());
        $this->assertEquals('Baz', $facets[2]->get_id());
    }

    private function get_cluster_input_data()
    {
        return json_decode('{
            "header": {
                "query": {
                    "userId": "user_52efac67e5abc",
                    "sessionId": "3127b3a7-3de0-4cc9-b152-ef33d0c28a1a",
                    "date": "2014-02-28T13:22:56+0000",
                    "queryParam": [],
                    "mainCtx": { "textQuery": "" },
                    "textQuery": ""
                },
                "user": {
                    "requestMethod": "GET",
                    "agent": "Mozilla/5.0 (X11; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0 Iceweasel/24.0",
                    "address": "10.61.8.236",
                    "output": {
                        "format": "JSON",
                        "encoding": "gzip",
                        "charset": "UTF-8"
                    }
                },
                "performance": {
                    "durationMs": 13
                },
                "info": { }
            },
            "replySet": [
                {
                    "meta": {
                        "uri": "Catalog",
                        "totalItems": 61,
                        "totalItemsIsExact": true,
                        "pageItems": 20,
                        "firstPageItem": 1,
                        "lastPageItem": 20,
                        "durationMs": 6,
                        "cluster": "marketing",
                        "firstPaFId": 1,
                        "lastPaFId": 1,
                        "producer": "SEARCH",
                        "totalItemsInClusters": 2,
                        "nbClusters": 2
                    },
                    "facets": {
                        "facet": [
                            {
                                "afs:t": "FacetTree",
                                "node": [
                                    {
                                        "key": "OPERATION_9",
                                        "labels": [
                                            {
                                                "lang": "FR",
                                                "label": "5% sur les produtis textiles de plus de 100 euros"
                                            }
                                        ],
                                        "items": 9
                                    },
                                    {
                                        "key": "OPERATION_8",
                                        "items": 6
                                    }
                                ],
                                "layout": "TREE",
                                "type": "STRING",
                                "id": "marketing",
                                "labels": [
                                    {
                                        "lang": "FR",
                                        "label": "Marketing"
                                    }
                                ]
                            }
                        ]
                    },
                    "content": {
                        "cluster": [
                            {
                                "id": "OPERATION_8",
                                "totalItems": 6,
                                "totalItemsIsExact": true,
                                "pageItems": 1,
                                "firstPageItem": 1,
                                "lastPageItem": 1,
                                "reply": [
                                    {
                                        "docId": 64,
                                        "uri": "166_en",
                                        "title": [
                                            {
                                                "afs:t": "KwicString",
                                                "text": "HTC Touch Diamond"
                                            }
                                        ],
                                        "relevance": { "rank": 1 },
                                        "clientData": [
                                            {
                                                "contents": "<product/>",
                                                "id": "main",
                                                "mimeType": "text/xml"
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                "id": "OPERATION_9",
                                "totalItems": 9,
                                "totalItemsIsExact": true,
                                "pageItems": 1,
                                "firstPageItem": 1,
                                "lastPageItem": 1,
                                "reply": [
                                    {
                                        "docId": 16,
                                        "uri": "112_fr",
                                        "title": [
                                            {
                                                "afs:t": "KwicString",
                                                "text": "ECCO Womens Golf Flexor Chaussures de golf"
                                            }
                                        ],
                                        "relevance": { "rank": 2 },
                                        "clientData": [
                                            {
                                                "contents": "<product/>",
                                                "id": "main",
                                                "mimeType": "text/xml"
                                            }
                                        ]
                                    }
                                ]
                            }
                        ],
                        "reply": [
                            {
                                "docId": 63,
                                "uri": "165_en",
                                "title": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "My Computer"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "test description"
                                    }
                                ],
                                "relevance": { "rank": 3 },
                                "clientData": [
                                    {
                                        "contents": "<product/>",
                                        "id": "main",
                                        "mimeType": "text/xml"
                                    }
                                ]
                            }
                        ]
                    },
                    "pager": {
                        "nextPage": 2,
                        "currentPage": 1,
                        "page": [ 1, 2, 3, 4 ]
                    }
                }
            ]
        }');
    }

    public function testClustering()
    {
        $input = $this->get_cluster_input_data();

        $config = new AfsHelperConfiguration();
        $query = new AfsQuery();
        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);

        $this->assertEquals('marketing', $helper->get_meta()->get_cluster_id());
        $this->assertEquals('Marketing', $helper->get_meta()->get_cluster_label());

        $this->assertTrue($helper->has_cluster());

        # First cluster from list of clusters
        $clusters = $helper->get_clusters();
        $this->assertEquals(2, count($clusters));
        $cluster = reset($clusters);
        $this->assertEquals('"OPERATION_8"', $cluster->get_id());
        $this->assertEquals('OPERATION_8', $cluster->get_label());
        $this->assertTrue($cluster->has_reply());
        $replies = $cluster->get_replies();
        $this->assertEquals(1, count($replies));
        $this->assertEquals('166_en', $replies[0]->get_uri());

        # Second cluster from list of clusters
        $cluster = next($clusters);
        $this->assertEquals('"OPERATION_9"', $cluster->get_id());
        $this->assertEquals('5% sur les produtis textiles de plus de 100 euros', $cluster->get_label());
        $this->assertTrue($cluster->has_reply());
        $replies = $cluster->get_replies();
        $this->assertEquals(1, count($replies));
        $this->assertEquals('112_fr', $replies[0]->get_uri());

        # Overspill
        $this->assertTrue($helper->has_reply());
        $replies = $helper->get_replies();
        $this->assertEquals(1, count($replies));
        $reply = reset($replies);
        $this->assertEquals('165_en', $reply->get_uri());

        # Merge replies from all cluster replies
        $replies = $helper->get_cluster_replies();
        $this->assertEquals(2, count($replies));
        $this->assertEquals('166_en', $replies[0]->get_uri());
        $this->assertEquals('112_fr', $replies[1]->get_uri());

        # Merge all replies: cluster replies and overspill
        $replies = $helper->get_all_replies();
        $this->assertEquals(3, count($replies));
        $this->assertEquals('166_en', $replies[0]->get_uri());
        $this->assertEquals('112_fr', $replies[1]->get_uri());
        $this->assertEquals('165_en', $replies[2]->get_uri());
    }

    public function testClusteringAsArray()
    {
        $input = $this->get_cluster_input_data();

        $config = new AfsHelperConfiguration();
        $query = new AfsQuery();
        $helper = new AfsReplysetHelper($input->replySet[0], $query, $config);
        $result = $helper->format();

        $this->assertEquals('marketing', $result['meta']['cluster']);
        $this->assertEquals('Marketing', $result['meta']['cluster_label']);

        $clusters = $result['clusters'];
        $this->assertFalse(empty($clusters));
        $this->assertEquals(2, count($clusters));

        # First cluster from list of clusters
        $cluster = each($clusters);
        $this->assertEquals('"OPERATION_8"', $cluster['key']);
        $cluster = $cluster['value'];
        $this->assertEquals('"OPERATION_8"', $cluster['id']);
        $this->assertEquals('OPERATION_8', $cluster['label']);
        $replies = $cluster['replies'];
        $this->assertFalse(empty($replies));
        $this->assertEquals(1, count($replies));
        $this->assertEquals('166_en', $replies[0]['uri']);

        # Second cluster from list of clusters
        $cluster = each($clusters);
        $this->assertEquals('"OPERATION_9"', $cluster['key']);
        $cluster = $cluster['value'];
        $this->assertEquals('"OPERATION_9"', $cluster['id']);
        $this->assertEquals('5% sur les produtis textiles de plus de 100 euros', $cluster['label']);
        $replies = $cluster['replies'];
        $this->assertFalse(empty($replies));
        $this->assertEquals(1, count($replies));
        $this->assertEquals('112_fr', $replies[0]['uri']);

        # Overspill
        $replies = $result['replies'];
        $this->assertFalse(empty($replies));
        $this->assertEquals(1, count($replies));
        $reply = $replies[0];
        $this->assertEquals('165_en', $reply['uri']);
    }
}


