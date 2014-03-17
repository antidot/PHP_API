<?php ob_start();
require_once 'AFS/SEARCH/afs_query_coder.php';


class QueryCoderTest extends PHPUnit_Framework_TestCase
{
    public function testBuildQueryFromEmptyParameter()
    {
        $coder = new AfsQueryCoder();
        $query = $coder->build_query(array());
        $this->assertEquals(0, count($query->get_filters()));
        $this->assertFalse($query->has_query());
        // and so on
    }

    public function testBuildQueryFromQueryParameter()
    {
        $coder = new AfsQueryCoder();
        $query = $coder->build_query(array('query' => 'FOO'));
        $this->assertEquals(0, count($query->get_filters()));
        $this->assertTrue($query->has_query());
        $this->assertEquals('FOO', $query->get_query());
        // and so on
    }

    public function testBuildQueryFromFilterParameter()
    {
        $coder = new AfsQueryCoder();
        $query = $coder->build_query(array('filter' => 'FOO_bar_baz'));
        $this->assertFalse($query->has_query());
        $this->assertEquals(1, count($query->get_filters()));
        $this->assertTrue($query->has_filter('FOO', 'bar'));
        $this->assertTrue($query->has_filter('FOO', 'baz'));
        $this->assertFalse($query->has_filter('FOO', 'bat'));
        // and so on
    }

    public function testBuildQueryFromUnknownParameter()
    {
        $coder = new AfsQueryCoder();
        $query = $coder->build_query(array('X' => '42', 'Y' => '666'));
        $this->assertEquals(0, count($query->get_filters()));
        $this->assertFalse($query->has_query());
        // and so on
    }
}
