<?php ob_start();
require_once "AFS/SEARCH/afs_concept_helper.php";

class ConceptHelperTest extends PHPUnit_Framework_TestCase
{
    public function testWithDefaultConceptName()
    {
        $input = json_decode('{
                    "meta": {
                        "uri": "concept",
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
                                            },
                                            {
                                                "afs:t": "QueryText",
                                                "text": " et "
                                            },
                                            {
                                                "afs:t": "QueryMatch",
                                                "text": "divorce",
                                                "uri": [ "lnf:taxo#QI-thm1344998" ]
                                            }
                                        ]
                                    },
                                    "concepts": {
                                        "concept": [
                                            {
                                                "uri": "lnf:taxo#QI-thm2009862",
                                                "contents": "foo"
                                            },
                                            {
                                                "uri": "lnf:taxo#QI-thm1344998",
                                                "contents": "bar"
                                            }
                                        ]
                                    }
                                }
                            }
                        ]
                    }
                }');
        $mgr = new AfsConceptManager();
        $mgr->add_concept($input);

        $helper = $mgr->get_concept();
        // is equivalent to
        $helper = $mgr->get_concept('concept');
        try {
            $mgr->get_concept('Unknown');
            $this->fail('Should have raised exception on unknown feed');
        } catch (OutOfBoundsException $e) { }
        $items = $helper->get_items();
        $this->assertEquals(3, count($items));

        $key_value = each($items);
        $this->assertEquals('mariage', $key_value['value']->get_text());
        $this->assertTrue($key_value['value']->has_concept());
        $item = each($key_value['value']->get_data());
        $this->assertEquals('foo', $item['value']);
        $this->assertEquals('lnf:taxo#QI-thm2009862', $item['key']);

        $key_value = each($items);
        $this->assertEquals(' et ', $key_value['value']->get_text());
        $this->assertFalse($key_value['value']->has_concept());

        $key_value = each($items);
        $this->assertEquals('divorce', $key_value['value']->get_text());
        $this->assertTrue($key_value['value']->has_concept());
        $item = each($key_value['value']->get_data());
        $this->assertEquals('bar', $item['value']);
        $this->assertEquals('lnf:taxo#QI-thm1344998', $item['key']);
    }

    public function testWithSpecificConceptName()
    {
        $input = json_decode('{
                    "meta": {
                        "uri": "Specific",
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
                }');
        $mgr = new AfsConceptManager();
        $mgr->add_concept($input);

        $helper = $mgr->get_concept();
        // is equivalent to
        $helper = $mgr->get_concept('Specific');
        try {
            $mgr->get_concept(AFS_DEFAULT_CONCEPT);
            $this->fail('Should have raised exception on default concept name');
        } catch (OutOfBoundsException $e) { }
        $items = $helper->get_items();
        $this->assertEquals(1, count($items));

        $key_value = each($items);
        $this->assertEquals('mariage', $key_value['value']->get_text());
        $this->assertTrue($key_value['value']->has_concept());
        $item = each($key_value['value']->get_data());
        $this->assertEquals('foo', $item['value']);
        $this->assertEquals('lnf:taxo#QI-thm2009862', $item['key']);
    }

    public function testWithMultipleConcepts()
    {
        $input1 = json_decode('{
                    "meta": {
                        "uri": "Specific",
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
                }');
        $input2 = json_decode('{
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
                }');
        $mgr = new AfsConceptManager();
        $mgr->add_concept($input1);
        $mgr->add_concept($input2);

        try {
            $mgr->get_concept();
            $this->fail('Should have raised exception on default concept name');
        } catch (OutOfBoundsException $e) { }
        $helper = $mgr->get_concept('Specific');
    }

    public function testWithNoConcept()
    {
        $input = json_decode('{
                    "meta": {
                        "uri": "fooConcept",
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
                                                "afs:t": "QueryText",
                                                "text": "toto"
                                            }
                                        ]
                                    },
                                    "concepts": { }
                                }
                            }
                        ]
                    }
                }');
        $mgr = new AfsConceptManager();
        $mgr->add_concept($input);
        $this->assertEquals(0, count($mgr->get_concepts()));
    }
}
