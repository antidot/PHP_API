<?php

require_once 'AFS/SEARCH/afs_facet_default.php';

class FacetDefaultTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultParameters()
    {
        $default = new AfsFacetDefault();
        $this->assertEquals(1000, $default->get_nb_replies());
        $this->assertNull($default->get_sort_order());
    }

    public function testNbReplies()
    {
        $default = new AfsFacetDefault();
        $default->set_nb_replies(42);
        $this->assertEquals(42, $default->get_nb_replies());
    }

    public function testSortOrder()
    {
        $default = new AfsFacetDefault();
        $default->set_sort_order(AfsFacetValuesSortMode::ITEMS, AfsSortOrder::DESC);
        $this->assertFalse(is_null($default->get_sort_order()));
        $this->assertEquals(AfsFacetValuesSortMode::ITEMS, $default->get_sort_order()->mode);
        $this->assertEquals(AfsSortOrder::DESC, $default->get_sort_order()->order);
    }

    public function testFormatWithDefaultValues()
    {
        $default = new AfsFacetDefault();
        $default->set_nb_replies(42);
        $this->assertEquals(array('replies=42'), $default->format());
    }
    public function testFormatWithSortOrder()
    {
        $default = new AfsFacetDefault();
        $default->set_nb_replies(42);
        $default->set_sort_order(AfsFacetValuesSortMode::ITEMS, AfsSortOrder::DESC);
        $this->assertEquals(array('replies=42', 'sort=items', 'order=DESC'), $default->format());
    }
}
