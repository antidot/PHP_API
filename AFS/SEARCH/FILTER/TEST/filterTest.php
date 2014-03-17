<?php ob_start();

require_once 'AFS/SEARCH/FILTER/afs_filter.php';


class FilterTest extends PHPUnit_Framework_TestCase
{
    public function testFilterEqual()
    {
        $filter = filter('ID')->equal;
        $this->assertEquals('ID=', $filter->to_string());
    }
    public function testFilterNotEqual()
    {
        $filter = filter('ID')->not_equal;
        $this->assertEquals('ID!=', $filter->to_string());
    }
    public function testFilterLess()
    {
        $filter = filter('ID')->less;
        $this->assertEquals('ID<', $filter->to_string());
    }
    public function testFilterLessEqual()
    {
        $filter = filter('ID')->less_equal;
        $this->assertEquals('ID<=', $filter->to_string());
    }
    public function testFilterGreater()
    {
        $filter = filter('ID')->greater;
        $this->assertEquals('ID>', $filter->to_string());
    }
    public function testFilterGreaterEqual()
    {
        $filter = filter('ID')->greater_equal;
        $this->assertEquals('ID>=', $filter->to_string());
    }

    public function testFilterBadOperator()
    {
        try
        {
            filter('ID')->less_than_or_equal_to;
            $this->fail('Should have raised an exception for invalid operator');
        } catch  (AfsUnknownOperatorException $e) { }
    }

    public function testFilterEqualValue()
    {
        $filter = filter('ID')->equal->value('42');
        $this->assertEquals('ID=42', $filter->to_string());
    }
    public function testFilterNotEqualValue()
    {
        $filter = filter('ID')->not_equal->value('666');
        $this->assertEquals('ID!=666', $filter->to_string());
    }
    public function testFilterLessValue()
    {
        $filter = filter('ID')->less->value('666');
        $this->assertEquals('ID<666', $filter->to_string());
    }
    public function testFilterLessEqualValue()
    {
        $filter = filter('ID')->less_equal->value('42');
        $this->assertEquals('ID<=42', $filter->to_string());
    }
    public function testFilterGreaterValue()
    {
        $filter = filter('ID')->greater->value('42');
        $this->assertEquals('ID>42', $filter->to_string());
    }
    public function testFilterGreaterEqualValue()
    {
        $filter = filter('ID')->greater_equal->value('666');
        $this->assertEquals('ID>=666', $filter->to_string());
    }

    public function testIntegerValue()
    {
        $filter = filter('ID')->equal->value(42);
        $this->assertEquals('ID=42', $filter->to_string());
    }
    public function testFloatValue()
    {
        $filter = filter('ID')->equal->value(66.6);
        $this->assertEquals('ID=66.6', $filter->to_string());
    }
    public function testTrueBoolValue()
    {
        $filter = filter('ID')->equal->value(true);
        $this->assertEquals('ID=true', $filter->to_string());
    }
    public function testFalseBoolValue()
    {
        $filter = filter('ID')->equal->value(FALSE);
        $this->assertEquals('ID=false', $filter->to_string());
    }

}
