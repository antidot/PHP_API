<?php
require_once "afs_facet_helper.php";

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

        $facet_mgr = new AfsFacetManager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AFS_FACET_BOOL, AFS_FACET_ADD));
        $helper = new AfsFacetHelper($input, $facet_mgr, new AfsQuery());
        $this->assertEquals($helper->get_label(), "Faceta booleana");
        $this->assertEquals('BOOL', $helper->get_id());
        $this->assertEquals(AFS_FACET_BOOL, $helper->get_type());
        $this->assertEquals(AFS_FACET_TREE, $helper->get_layout());
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
            "layout": "INTERVAL",
            "type": "STRING",
            "id": "FOO",
            "labels": [
                {
                    "label": "String facet"
                }
             ],
             "sticky": "true" }');

        $facet_mgr = new AfsFacetManager();
        $facet_mgr->add_facet(new AfsFacet('FOO', AFS_FACET_BOOL, AFS_FACET_ADD));
        $helper = new AfsFacetHelper($input, $facet_mgr, new AfsQuery());
        $this->assertEquals($helper->get_label(), "String facet");
        $this->assertEquals('FOO', $helper->get_id());
        $this->assertEquals(AFS_FACET_STRING, $helper->get_type());
        $this->assertEquals(AFS_FACET_INTERVAL, $helper->get_layout());
        $this->assertEquals(true, $helper->is_sticky());
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

        $facet_mgr = new AfsFacetManager();
        $facet_mgr->add_facet(new AfsFacet('ADVANCED_INTERVAL_DATE', AFS_FACET_DATE));
        $query = new AfsQuery();
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $elems = $builder->create_elements('ADVANCED_INTERVAL_DATE', $input, null, AFS_HELPER_FORMAT);

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

        $facet_mgr = new AfsFacetManager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AFS_FACET_BOOL));
        $query = new AfsQuery();
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $elems = $builder->create_elements('BOOL', $input, null, AFS_HELPER_FORMAT);

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

        $facet_mgr = new AfsFacetManager();
        $facet_mgr->add_facet(new AfsFacet('TREE_DATE', AFS_FACET_DATE));
        $query = new AfsQuery();
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $elems = $builder->create_elements('TREE_DATE', $input, null, AFS_HELPER_FORMAT);

        $this->assertEquals(count($elems), 1);
        $elem = reset($elems);
        $this->assertEquals($elem->label, '2010');
        $this->assertEquals($elem->count, 24);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('TREE_DATE', '2010'));
        $this->assertEquals(count($elem->values), 1);
        $elem = $elem->values[0];
        $this->assertEquals($elem->label, '03');
        $this->assertEquals($elem->count, 14);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('TREE_DATE', '2010-03'));
        $this->assertEquals(count($elem->values), 1);
        $elem = $elem->values[0];
        $this->assertEquals($elem->label, '07');
        $this->assertEquals($elem->count, 4);
        $this->assertFalse($elem->active);
        $this->assertTrue($elem->query->has_filter('TREE_DATE', '2010-03-07'));
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

        $facet_mgr = new AfsFacetManager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AFS_FACET_BOOL));
        $query = new AfsQuery();
        $query = $query->add_filter('BOOL', 'false');
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $elems = $builder->create_elements('BOOL', $input, null, AFS_HELPER_FORMAT);

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

        $facet_mgr = new AfsFacetManager();
        $facet_mgr->add_facet(new AfsFacet('BOOL', AFS_FACET_BOOL, AFS_FACET_ADD));
        $query = new AfsQuery();
        $query = $query->add_filter('BOOL', 'false');
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $elems = $builder->create_elements('BOOL', $input, null, AFS_HELPER_FORMAT);

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
}
