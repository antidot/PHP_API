<?php ob_start();
require_once "AFS/SEARCH/afs_facet_helper.php";
require_once "AFS/SEARCH/afs_query.php";
require_once "AFS/SEARCH/afs_response_helper.php";

class FacetHelperTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveFacetLabel()
    {
        $input = json_decode('{
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
                    "lang": "ES",
                    "region": "ES",
                    "label": "Faceta booleana"
                },
                {
                    "lang": "FR",
                    "label": "Facette booléenne"
                },
                {
                    "label": "Boolean facet"
                }
            ] }');

        $config = new AfsHelperConfiguration();
        $helper = new AfsFacetHelper($input, new AfsQuery(), $config);
        $this->assertEquals($helper->get_label(), "Faceta booleana");
        $this->assertEquals('BOOL', $helper->get_id());
        $this->assertEquals(AfsFacetType::BOOL_TYPE, $helper->get_type());
        $this->assertEquals(AfsFacetLayout::TREE, $helper->get_layout());
        $this->assertEquals(false, $helper->is_sticky());
    }

    public function testRetrieveStickyness()
    {
        $input = json_decode('{
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
                }
            ],
            "layout": "TREE",
            "type": "BOOL",
            "id": "FOO",
            "labels": [
                {
                    "label": "String facet"
                }
             ],
             "sticky": "true" }');

        $config = new AfsHelperConfiguration();
        $helper = new AfsFacetHelper($input, new AfsQuery(), $config);
        $this->assertEquals($helper->get_label(), "String facet");
        $this->assertEquals('FOO', $helper->get_id());
        $this->assertEquals(AfsFacetType::BOOL_TYPE, $helper->get_type());
        $this->assertEquals(AfsFacetLayout::TREE, $helper->get_layout());
        $this->assertEquals(true, $helper->is_sticky());
    }

    public function testFacetValueNoMetaAvailable()
    {
        $input = json_decode('{
            "afs:t": "FacetTree",
            "node": [ {
                    "key": "false",
                    "labels": [ { "label": "BAD" } ],
                    "items": 67
                } ],
            "layout": "TREE",
            "type": "BOOL",
            "id": "BOOL",
            "labels": [ { "label": "Boolean facet" } ] }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $helper = new AfsFacetHelper($input, new AfsQuery(), $config);
        $elems = $helper->get_elements();
        $this->assertEquals(1, count($elems));
        $this->assertEquals(0, count($elems[0]->get_meta()));
    }

    public function testFacetValueOneMetaAvailable()
    {
        $input = json_decode('{
            "afs:t": "FacetTree",
            "node": [ {
                    "key": "false",
                    "labels": [ { "label": "BAD" } ],
                    "items": 67,
                    "meta": [ {
                        "key": "meta_id",
                        "value": "meta_value"
                    } ]
                } ],
            "layout": "TREE",
            "type": "BOOL",
            "id": "BOOL",
            "labels": [ { "label": "Boolean facet" } ]
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $helper = new AfsFacetHelper($input, new AfsQuery(), $config);
        $elems = $helper->get_elements();

        $this->assertEquals(1, count($elems));
        $metas = $elems[0]->get_meta();
        $this->assertEquals(1, count($metas));
        foreach ($metas as $meta_key => $meta_value) {
            $this->assertEquals('meta_id', $meta_key);
            $this->assertEquals('meta_value', $meta_value);
        }
        $this->assertEquals('meta_value', $elems[0]->get_meta('meta_id'));
    }

    public function testFacetValueMultipleMetaAvailable()
    {
        $input = json_decode('{
            "afs:t": "FacetTree",
            "node": [ {
                    "key": "false",
                    "labels": [ { "label": "BAD" } ],
                    "items": 67,
                    "meta": [
                        {
                            "key": "meta_id_1",
                            "value": "meta_value_1"
                        },
                        {
                            "key": "meta_id_2",
                            "value": "meta_value_2"
                        } ]
                } ],
            "layout": "TREE",
            "type": "BOOL",
            "id": "BOOL",
            "labels": [ { "label": "Boolean facet" } ]
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $helper = new AfsFacetHelper($input, new AfsQuery(), $config);
        $elems = $helper->get_elements();

        $this->assertEquals(1, count($elems));
        $metas = $elems[0]->get_meta();
        $this->assertEquals(2, count($metas));
        for ($i = 1; $i < 2; $i++) {
            $res = each($metas);
            $this->assertEquals('meta_id_' . $i, $res['key']);
            $this->assertEquals('meta_value_' . $i, $res['value']);
        }
        $this->assertEquals('meta_value_1', $elems[0]->get_meta('meta_id_1'));
        $this->assertEquals('meta_value_2', $elems[0]->get_meta('meta_id_2'));
    }

    public function testFacetValueWrongMetaRequested()
    {
        $input = json_decode('{
            "afs:t": "FacetTree",
            "node": [ {
                    "key": "false",
                    "labels": [ { "label": "BAD" } ],
                    "items": 67,
                    "meta": [
                        {
                            "key": "meta_id_1",
                            "value": "meta_value_1"
                        },
                        {
                            "key": "meta_id_2",
                            "value": "meta_value_2"
                        } ]
                } ],
            "layout": "TREE",
            "type": "BOOL",
            "id": "BOOL",
            "labels": [ { "label": "Boolean facet" } ]
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $helper = new AfsFacetHelper($input, new AfsQuery(), $config);
        $elems = $helper->get_elements();

        $this->assertEquals(1, count($elems));
        $metas = $elems[0]->get_meta();
        $this->assertEquals(2, count($metas));
        try {
            $elems[0]->get_meta('unknown_meta_id');
            $this->fail('Should have raised an exception on unknown meta id');
        } catch (OutOfBoundsException $e) { }
    }

    public function testFacetValueMultipleMetaAvailableInArrayFormat()
    {
        $input = json_decode('{
            "afs:t": "FacetTree",
            "node": [ {
                    "key": "false",
                    "labels": [ { "label": "BAD" } ],
                    "items": 67,
                    "meta": [
                        {
                            "key": "meta_id_1",
                            "value": "meta_value_1"
                        },
                        {
                            "key": "meta_id_2",
                            "value": "meta_value_2"
                        } ]
                } ],
            "layout": "TREE",
            "type": "BOOL",
            "id": "BOOL",
            "labels": [ { "label": "Boolean facet" } ]
        }');

        $config = new AfsHelperConfiguration();
        $helper = new AfsFacetHelper($input, new AfsQuery(), $config);
        $elems = $helper->get_elements();

        $this->assertEquals(1, count($elems));
        $metas = $elems[0]->meta;
        $this->assertEquals(2, count($metas));
        for ($i = 1; $i < 2; $i++) {
            $res = each($metas);
            $this->assertEquals('meta_id_' . $i, $res['key']);
            $this->assertEquals('meta_value_' . $i, $res['value']);
        }
    }


    public function testFacetElementBuilderOnInterval()
    {
        $input = json_decode('{
            "afs:t": "FacetInterval",
            "interval": [
                {
                    "key": "[\"2009-10-02\" .. \"2013-10-01\"[",
                    "items": 109
                },
                {
                    "key": "[\"2010-10-02\" .. \"2013-10-01\"[",
                    "items": 97
                }
            ],
            "layout": "INTERVAL",
            "type": "DATE",
            "id": "ADVANCED_INTERVAL_DATE",
            "labels": [
                {
                    "label": "Advanced date interval"
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $facet_mgr = $config->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('ADVANCED_INTERVAL_DATE', AfsFacetType::DATE_TYPE, AfsFacetLayout::INTERVAL));
        $query = new AfsQuery();
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $elems = $builder->create_elements('ADVANCED_INTERVAL_DATE', $input, $config);

        $this->assertEquals(count($elems), 2);
        $elem = reset($elems);
        $this->assertEquals($elem->label, '["2009-10-02" .. "2013-10-01"[');
        $this->assertEquals('["2009-10-02" .. "2013-10-01"[', $elem->key);
        $this->assertEquals($elem->count, 109);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('ADVANCED_INTERVAL_DATE', '["2009-10-02" .. "2013-10-01"['));
        $this->assertEquals(count($elem->values), 0);
        next($elems);
        $elem = current($elems);
        $this->assertEquals($elem->label, '["2010-10-02" .. "2013-10-01"[');
        $this->assertEquals('["2010-10-02" .. "2013-10-01"[', $elem->key);
        $this->assertEquals($elem->count, 97);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('ADVANCED_INTERVAL_DATE', '["2010-10-02" .. "2013-10-01"['));
        $this->assertEquals(count($elem->values), 0);
    }

    public function testFacetElementBuilderOnNode()
    {
        $input = json_decode('{
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
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $facet_mgr = $config->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AfsFacetType::BOOL_TYPE));
        $query = new AfsQuery();
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $elems = $builder->create_elements('BOOL', $input, $config);

        $this->assertEquals(count($elems), 2);
        $elem = reset($elems);
        $this->assertEquals($elem->label, 'BAD');
        $this->assertEquals($elem->count, 67);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('BOOL', 'false'));
        $this->assertEquals(count($elem->values), 0);
        next($elems);
        $elem = current($elems);
        $this->assertEquals($elem->label, 'GOOD');
        $this->assertEquals($elem->count, 133);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('BOOL', 'true'));
        $this->assertEquals(count($elem->values), 0);
    }

    public function testFacetElementBuilderOnTreeNode()
    {
        $input = json_decode('{
            "afs:t": "FacetTree",
            "node": [
                {
                    "key": "2010",
                    "labels": [
                        {
                            "lang": "FR",
                            "region": "FR",
                            "label": "2010"
                        }
                    ],
                    "items": 24,
                    "node": [
                        {
                            "key": "2010-03",
                            "labels": [
                                {
                                    "lang": "FR",
                                    "region": "FR",
                                    "label": "03"
                                }
                            ],
                            "items": 14,
                            "node": [
                                {
                                    "key": "2010-03-07",
                                    "labels": [
                                        {
                                            "lang": "FR",
                                            "region": "FR",
                                            "label": "07"
                                        }
                                    ],
                                    "items": 4
                                }
                            ]
                        }
                    ]
                }
            ],
            "layout": "TREE",
            "type": "DATE",
            "id": "TREE_DATE",
            "labels": [
                {
                    "label": "Tree date"
                }
            ]
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $facet_mgr = $config->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('TREE_DATE', AfsFacetType::DATE_TYPE));
        $query = new AfsQuery();
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $elems = $builder->create_elements('TREE_DATE', $input, $config);

        $this->assertEquals(count($elems), 1);
        $elem = reset($elems);
        $this->assertEquals($elem->label, '2010');
        $this->assertEquals($elem->count, 24);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('TREE_DATE', '"2010"'));
        $this->assertEquals(count($elem->values), 1);
        $elem = $elem->values[0];
        $this->assertEquals($elem->label, '03');
        $this->assertEquals($elem->count, 14);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('TREE_DATE', '"2010-03"'));
        $this->assertEquals(count($elem->values), 1);
        $elem = $elem->values[0];
        $this->assertEquals($elem->label, '07');
        $this->assertEquals($elem->count, 4);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('TREE_DATE', '"2010-03-07"'));
        $this->assertEquals(count($elem->values), 0);
    }

    public function testFacetElementBuilderReplaceFilter()
    {
        $input = json_decode('{
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
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $facet_mgr = $config->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AfsFacetType::BOOL_TYPE, AfsFacetLayout::TREE, AfsFacetMode::SINGLE_MODE));
        $query = new AfsQuery();
        $query = $query->add_filter('BOOL', 'false');
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $elems = $builder->create_elements('BOOL', $input, $config);

        $this->assertEquals(count($elems), 2);
        $elem = reset($elems);
        $this->assertEquals($elem->label, 'BAD');
        $this->assertEquals($elem->count, 67);
        $this->assertTrue($elem->active);
        $this->assertFalse($elem->query->has_filter('BOOL', 'false'));
        $this->assertEquals(count($elem->values), 0);
        next($elems);
        $elem = current($elems);
        $this->assertEquals($elem->label, 'GOOD');
        $this->assertEquals($elem->count, 133);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('BOOL', 'true'));
        $this->assertFalse($elem->query->has_filter('BOOL', 'false'));
        $this->assertEquals(count($elem->values), 0);
    }

    public function testFacetElementBuilderAddFilter()
    {
        $input = json_decode('{
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
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $facet_mgr = $config->get_facet_manager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AfsFacetType::BOOL_TYPE, AfsFacetLayout::TREE, AfsFacetMode::OR_MODE));
        $query = new AfsQuery();
        $query = $query->add_filter('BOOL', 'false');
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $elems = $builder->create_elements('BOOL', $input, $config);

        $this->assertEquals(count($elems), 2);
        $elem = reset($elems);
        $this->assertEquals($elem->label, 'BAD');
        $this->assertEquals($elem->count, 67);
        $this->assertTrue($elem->active);
        $this->assertFalse($elem->query->has_filter('BOOL', 'false'));
        $this->assertEquals(count($elem->values), 0);
        next($elems);
        $elem = current($elems);
        $this->assertEquals($elem->label, 'GOOD');
        $this->assertEquals($elem->count, 133);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('BOOL', 'true'));
        $this->assertTrue($elem->query->has_filter('BOOL', 'false'));
        $this->assertEquals(count($elem->values), 0);
    }

    public function testFacetWithoutLabel()
    {
        $input = json_decode('{
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
            "id": "BOOOOL"
        }');

        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $query = new AfsQuery();
        $facet = new AfsFacetHelper($input, $query, $config);

        $this->assertEquals('BOOOOL', $facet->get_label());
    }
}
