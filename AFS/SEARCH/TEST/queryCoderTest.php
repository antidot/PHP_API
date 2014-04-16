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

    public function testWriteCustomParameters()
    {
        $coder = new AfsQueryCoder();
        $query = $coder->build_query(array("query" => "u2s!57e2"));
        $coder->set_custom_parameter("mycustomparameter", "mycustomvalue");
        $coder->set_custom_parameter("andanotherone", "withanothervalue");
        $this->assertTrue(strpos($coder->generate_link($query), "mycustomparameter=mycustomvalue") !== false);
        $this->assertTrue(strpos($coder->generate_link($query), "andanotherone=withanothervalue") !== false);
    }

    public function testReadCustomParameters()
    {
        $coder = new AfsQueryCoder();
        $custom_get = array("toto" => "tutu");
        $query = $coder->build_query($custom_get);
        $this->assertTrue(strpos($coder->generate_link($query), "toto=tutu") !== false);
    }
}
