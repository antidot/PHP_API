<?php ob_start();
require_once "AFS/SEARCH/afs_spellcheck_helper.php";
require_once "AFS/SEARCH/afs_query.php";
require_once "afs_version.php" ;

class spellcheckHelperTest extends PHPUnit_Framework_TestCase
{
    public function testSpellcheckHelperWithoutQueryCoder()
    {
        $input = json_decode('{
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
                                "suggestion": [
                                    {
                                        "items": [
                                            {
                                                "match": {
                                                    "text": "LIGNE"
                                                }
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                "docId": 2,
                                "uri": "Catalog",
                                "suggestion": [
                                    {
                                        "items": [
                                            {
                                                "match": {
                                                    "text": "LIGNE"
                                                }
                                            },
                                            {
                                                "text": {
                                                    "text": "ET",
                                                    "pre": " "
                                                }
                                            },
                                            {
                                                "match": {
                                                    "text": "PLUME",
                                                    "pre": " "
                                                }
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                }');
        $query = new AfsQuery();
        $query = $query->set_query('lige ET plum');
        $this->assertTrue($query->get_from() != AfsOrigin::SPELLCHECK);
        $mgr = new AfsSpellcheckManager($query, new AfsHelperConfiguration());
        $mgr->add_spellcheck($input);

        try {
            $mgr->get_spellcheck('foobar');
            $this->fail('Should have raised an exception');
        } catch (OutOfBoundsException $e) { }

        $spellcheck = $mgr->get_spellcheck('Catalog');
        $this->assertEquals(2, count($spellcheck));

        // This is also good because there is only one spellcheck result
        $spellcheck = $mgr->get_spellcheck();
        $this->assertEquals(2, count($spellcheck));

        $first = $spellcheck[0];
        $this->assertEquals('LIGNE', $first->get_raw_text());
        $this->assertEquals('<b>LIGNE</b>', $first->get_formatted_text());
        $this->assertEquals('LIGNE', $first->get_query()->get_query());
        $this->assertEquals(AfsOrigin::SPELLCHECK, $first->get_query()->get_from());

        $second = $spellcheck[1];
        $this->assertEquals('LIGNE ET PLUME', $second->get_raw_text());
        $this->assertEquals('<b>LIGNE</b> ET <b>PLUME</b>', $second->get_formatted_text());
        $this->assertEquals(AfsOrigin::SPELLCHECK, $second->get_query()->get_from());
    }

    public function testMultiSpellcheck()
    {
        $input1 = $this->get_spellcheck_input('Cot', 'FOO');
        $input2 = $this->get_spellcheck_input('Cat', 'BAR');

        $query = new AfsQuery();
        $query = $query->set_query('lige ET plum');
        $mgr = new AfsSpellcheckManager($query, new AfsHelperConfiguration());
        $mgr->add_spellcheck($input1);
        $mgr->add_spellcheck($input2);

        try {
            $mgr->get_spellcheck(); // invalid: 2 spellcheck replies are available
            $this->fail('Should have raised an exception');
        } catch (OutOfBoundsException $e) { }

        $spellcheck = $mgr->get_spellcheck('Cot');
        $first = $spellcheck[0];
        $this->assertEquals('FOO', $first->get_raw_text());
        $this->assertEquals('<b>FOO</b>', $first->get_formatted_text());
        $this->assertEquals('FOO', $first->get_query()->get_query());

        $spellcheck = $mgr->get_spellcheck('Cat');
        $second = $spellcheck[0];
        $this->assertEquals('BAR', $second->get_raw_text());
        $this->assertEquals('<b>BAR</b>', $second->get_formatted_text());
        $this->assertEquals('BAR', $second->get_query()->get_query());

        $spellchecks = $mgr->get_spellchecks();
        $this->assertEquals(2, count($spellchecks));
        foreach ($spellchecks as $feed => $spellcheck) {
            if ('Cot' == $feed) {
                $this->assertEquals('FOO', $spellcheck[0]->get_raw_text());
                $this->assertEquals('<b>FOO</b>', $spellcheck[0]->get_formatted_text());
            } elseif ('Cat' == $feed) {
                $this->assertEquals('BAR', $spellcheck[0]->get_raw_text());
                $this->assertEquals('<b>BAR</b>', $spellcheck[0]->get_formatted_text());
            } else {
                $this->fail('Unknown feed: ' . $feed);
            }
        }
    }

    private function get_spellcheck_input($feed, $match)
    {
        return json_decode('{
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
                                "uri": "' . $feed . '",
                                "title": [
                                    {
                                        "afs:t": "KwicMatch",
                                        "match": "' . $match . '"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "' . $match . '"
                                    }
                                ],
                                "suggestion": [
                                    {
                                        "items": [
                                            {
                                                "match": {
                                                    "text": "' . $match . '"
                                                }
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                }');
    }

    public function testSpellcheckWithSep()   // bug #2531
    {
        $input = json_decode('{
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
                                        "match": "Bar"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "Bar"
                                    }
                                ],
                                "suggestion": [
                                    {
                                        "items": [
                                            { "sep": { "text": "(" } },
                                            { "match": { "text": "FOO" } }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                }');
        $query = new AfsQuery();
        $mgr = new AfsSpellcheckManager($query, new AfsHelperConfiguration());
        $mgr->add_spellcheck($input);

        $spellcheck = $mgr->get_spellcheck('Catalog');
        $this->assertEquals(1, count($spellcheck));

        $first = $spellcheck[0];
        $this->assertEquals('(FOO', $first->get_raw_text());
        $this->assertEquals('(<b>FOO</b>', $first->get_formatted_text());
        $this->assertEquals('(FOO', $first->get_query()->get_query());
        $this->assertEquals(AfsOrigin::SPELLCHECK, $first->get_query()->get_from());
    }
}


