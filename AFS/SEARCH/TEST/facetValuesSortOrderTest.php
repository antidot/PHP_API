<?php
require_once 'AFS/SEARCH/afs_facet_values_sort_order.php';


class FacetValuesSortOrderTest extends PHPUnit_Framework_TestCase
{
    public function testValidSortModeAndSortOrder()
    {
        $s = new AfsFacetValuesSortOrder(AfsFacetValuesSortMode::ALPHA, AfsSortOrder::ASC);
        $this->assertEquals(AfsFacetValuesSortMode::ALPHA, $s->mode);
        $this->assertEquals(AfsSortOrder::ASC, $s->order);
        $this->assertEquals(array('alpha', 'ASC'), $s->format());
    }
    public function testInvalidSortModeAndValidSortOrder()
    {
        try {
            $s = new AfsFacetValuesSortOrder('::ALPHA', AfsSortOrder::ASC);
            $this->fail('Invalid sort mode should have raised exception!');
        } catch (InvalidArgumentException $e) { }
    }
    public function testValidSortModeAndInvalidSortOrder()
    {
        try {
            $s = new AfsFacetValuesSortOrder(AfsFacetValuesSortMode::ALPHA, '::ASC');
            $this->fail('Invalid sort order should have raised exception!');
        } catch (InvalidArgumentException $e) { }
    }
    public function testCopy()
    {
        $f = new AfsFacetValuesSortOrder(AfsFacetValuesSortMode::ALPHA, AfsSortOrder::ASC);
        $s = $f->copy();
        $f->mode = AfsFacetValuesSortMode::ITEMS;
        $f->order = AfsSortOrder::ASC;
        $this->assertEquals(AfsFacetValuesSortMode::ALPHA, $s->mode);
        $this->assertEquals(AfsSortOrder::ASC, $s->order);
        $this->assertEquals(array('alpha', 'ASC'), $s->format());
    }
}
