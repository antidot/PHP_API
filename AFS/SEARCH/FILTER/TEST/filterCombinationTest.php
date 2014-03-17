<?php ob_start();
require_once 'AFS/SEARCH/FILTER/afs_combinator_filter.php';


class FilterCombinationTest extends PHPUnit_Framework_TestCase
{
    public function testAndCombinator()
    {
        $result = filter('ID')->equal->value('value')->and->filter('FOO')->not_equal->value('bar');
        $this->assertEquals('ID=value and FOO!=bar', $result->to_string());
    }

    public function testOrCombinator()
    {
        $result = filter('ID')->equal->value('value')->or->filter('FOO')->not_equal->value('bar');
        $this->assertEquals('ID=value or FOO!=bar', $result->to_string());
    }

    public function testMultipleCombinators()
    {
        $result = filter('ID')->equal->value('value')->and->filter('FOO')->not_equal->value('bar')->or
            ->filter('ID')->equal->value('value')->or->filter('FOO')->not_equal->value('bar');
        $this->assertEquals('ID=value and FOO!=bar or ID=value or FOO!=bar', $result->to_string());
    }

    public function testBadCombinator()
    {
        try
        {
            filter('ID')->equal->value('value')->foo->filter('FOO')->not_equal->value('bar');
            $this->fail('Bad combinator should have raised exception!');
        } catch (AfsUnknownCombinatorException $e) {}
    }
}
