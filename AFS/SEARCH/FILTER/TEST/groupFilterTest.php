<?php ob_start();
require_once 'AFS/SEARCH/FILTER/afs_group_filter.php';


class GroupFilterTest extends PHPUnit_Framework_TestCase
{
    public function testGroupOneFilter()
    {
        $result = group(filter('ID')->equal->value('value'));
        $this->assertEquals('(ID=value)', $result->to_string());
    }

    public function testGroupOfCombinedFilters()
    {
        $result = group(filter('ID')->equal->value('value')->and->filter('FOO')->less->value('val'));
        $this->assertEquals('(ID=value and FOO<val)', $result->to_string());
    }

    public function testCombineGroupAndFilter()
    {
        $result = group(filter('ID')->equal->value('value'))->and->filter('FOO')->less->value('val');
        $this->assertEquals('(ID=value) and FOO<val', $result->to_string());
    }

    public function testCombineFilterAndGroup()
    {
        $result = filter('ID')->equal->value('value')->and->group(filter('FOO')->less->value('val'));
        $this->assertEquals('ID=value and (FOO<val)', $result->to_string());
    }

    public function testCombineGroups()
    {
        $result = group(filter('ID')->equal->value('value'))->and->group(filter('FOO')->less->value('val'));
        $this->assertEquals('(ID=value) and (FOO<val)', $result->to_string());
    }

    public function testGroupOfGroupsAndCombinedFilters()
    {
        $result = group(filter('ID')->equal->value('value')->and->filter('FOO')->equal->value('bar'))
            ->or->group(filter('ID')->equal->value('val')->and->filter('FOO')->equal->value('baz'))
            ->or->filter('YOP')->less->value('bla');
        $this->assertEquals('(ID=value and FOO=bar) or (ID=val and FOO=baz) or YOP<bla', $result->to_string());
    }
}
