<?php ob_start();
require_once 'AFS/SEARCH/FILTER/afs_combinator_filter.php';


function myBuilder($id, array $values)
{
    if (empty($values))
        return null;

    $value = array_shift($values);
    $result = filter($id)->equal->value($value);
    while (! empty($values))
        $result = $result->or->filter($id)->equal->value(array_shift($values));

    return $result;
}


class FilterBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testFilterBuilderNoValue()
    {
        $this->assertEquals(null, myBuilder('ID', array()));
    }

    public function testFilterBuilderOneValue()
    {
        $this->assertEquals('ID=v', myBuilder('ID', array('v'))->to_string());
    }

    public function testFilterBuilderMultipleValues()
    {
        $this->assertEquals('ID=v1 or ID=v2 or ID=v3', myBuilder('ID', array('v1', 'v2', 'v3'))->to_string());
    }
}
