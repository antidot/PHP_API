<?php
require_once "AFS/SEARCH/afs_query.php";


class QueryTest extends PHPUnit_Framework_TestCase
{
    public function testSetQuery()
    {
        $query = new AfsQuery();
        $query = $query->set_query('foo');
        $this->assertTrue($query->get_query() == 'foo');
    }
    public function testSetNewQueryValue()
    {
        $query = new AfsQuery();
        $query = $query->set_query('foo');
        $query = $query->set_query('bar');
        $this->assertFalse($query->get_query() == 'foo');
        $this->assertTrue($query->get_query() == 'bar');
    }

    public function testHasNoQuery()
    {
        $query = new AfsQuery();
        $this->assertFalse($query->has_query());
    }
    public function testHasQuery()
    {
        $query = new AfsQuery();
        $query = $query->set_query('foo');
        $this->assertTrue($query->has_query());
    }

    public function testAddFilterValue()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $this->assertTrue($query->has_filter('foo', 'bar'));
    }
    public function testAddSameFilterValue()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        try {
            $query = $query->add_filter('foo', 'bar');
            $this->assertTrue($query->has_filter('foo', 'bar'));
        } catch (Exception $e) {
            $this->fail('Cannot set same filter value twice!');
        }
    }
    public function testAddFilterValues()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $query = $query->add_filter('foo', 'baz');
        $this->assertTrue($query->has_filter('foo', 'bar'));
        $this->assertTrue($query->has_filter('foo', 'baz'));
    }
    public function testAddValuesToFilters()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $query = $query->add_filter('foo', 'baz');
        $query = $query->add_filter('fox', 'bat');
        $query = $query->add_filter('fox', 'bas');
        $this->assertTrue($query->has_filter('foo', 'bar'));
        $this->assertTrue($query->has_filter('foo', 'baz'));
        $this->assertTrue($query->has_filter('fox', 'bat'));
        $this->assertTrue($query->has_filter('fox', 'bas'));
    }

    public function testSetValueToFilter()
    {
        $query = new AfsQuery();
        $query = $query->set_filter('foo', 'bar');
        $this->assertTrue($query->has_filter('foo', 'bar'));
    }
    public function testOverwriteValueToFilter()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $query = $query->add_filter('foo', 'baz');
        $query = $query->set_filter('foo', 'foz');
        $this->assertFalse($query->has_filter('foo', 'bar'));
        $this->assertFalse($query->has_filter('foo', 'baz'));
        $this->assertTrue($query->has_filter('foo', 'foz'));
    }

    public function testHasNoFilter()
    {
        $query = new AfsQuery();
        $this->assertFalse($query->has_filter('foo', 'bar'));
    }
    public function testHasFilterWithWrongValue()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'baz');
        $this->assertFalse($query->has_filter('foo', 'bar'));
    }
    public function testHasFilterWithRightValue()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $this->assertTrue($query->has_filter('foo', 'bar'));
    }
    public function testHasFilterWithValueEqualToZero()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', '4');
        $this->assertTrue($query->has_filter('foo', '4'));
        $this->assertFalse($query->has_filter('foo', '0'));
    }

    public function testRemoveValueFromUnexistingFilter()
    {
        $query = new AfsQuery();
        try {
            $query->remove_filter('foo', 'bar');
        } catch (Exception $e) {
            $this->fail('Exception raised: ' . $e);
        }
    }
    public function testRemoveUnexistingFilterValue()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'baz');
        try {
            $query->remove_filter('foo', 'bar');
        } catch (Exception $e) {
            $this->fail('Exception raised: ' . $e);
        }
    }
    public function testRemoveExistingFilterValue()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $query = $query->remove_filter('foo', 'bar');
        $this->assertFalse($query->has_filter('foo', 'bar'));
    }

    public function testGetListOfValuesForUnexistingFilter()
    {
        $query = new AfsQuery();
        try {
            $values = $query->get_filter_values('foo');
        } catch (Exception $e) {
            return;
        }
        $this->fail('Getting values from unexisting filter should raise exception!');
    }
    public function testGetListOfFilterValues()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $query = $query->add_filter('foo', 'baz');
        $values = $query->get_filter_values('foo');
        $this->assertTrue(in_array('bar', $values));
        $this->assertTrue(in_array('baz', $values));
    }

    public function testGetEmptyListOfFilters()
    {
        $query = new AfsQuery();
        $filters = $query->get_filters();
        $this->assertTrue(empty($filters));
    }
    public function testGetListOfFilters()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $query = $query->add_filter('foz', 'baz');
        $filters = $query->get_filters();
        $this->assertFalse(empty($filters));
        $this->assertTrue(in_array('foo', $query->get_filters()));
        $this->assertTrue(in_array('foz', $query->get_filters()));
    }

    public function testHasNoFeedSet()
    {
        $query = new AfsQuery();
        $this->assertFalse($query->has_feed());
    }
    public function testHasFeedName()
    {
        $query = new AfsQuery();
        $query = $query->set_feed('foo');
        $this->assertTrue($query->has_feed());
        $this->assertTrue(in_array('foo', $query->get_feeds()));
    }
    public function testHasFeedNames()
    {
        $query = new AfsQuery();
        $query = $query->add_feed('foo');
        $query = $query->add_feed('bar');
        $this->assertTrue($query->has_feed());
        $this->assertTrue(in_array('foo', $query->get_feeds()));
        $this->assertTrue(in_array('bar', $query->get_feeds()));
    }
    public function testResetFeedName()
    {
        $query = new AfsQuery();
        $query = $query->add_feed('foo');
        $query = $query->add_feed('bar');
        $query = $query->set_feed('baz');
        $this->assertTrue($query->has_feed());
        $this->assertFalse(in_array('foo', $query->get_feeds()));
        $this->assertFalse(in_array('bar', $query->get_feeds()));
        $this->assertTrue(in_array('baz', $query->get_feeds()));
    }

    public function testDefaultPage()
    {
        $query = new AfsQuery();
        $this->assertTrue($query->get_page() == 1);
    }
    public function testSetPage()
    {
        $query = new AfsQuery();
        $query = $query->set_page(42);
        $this->assertTrue($query->get_page() == 42);
    }
    public function testResetPageOnNewQuery()
    {
        $query = new AfsQuery();
        $query = $query->set_page(42);
        $query = $query->set_query('foo');
        $this->assertTrue($query->get_page() == 1);
    }
    public function testResetPageOnNewFeed()
    {
        $query = new AfsQuery();
        $query = $query->set_page(42);
        $query = $query->set_feed('foo');
        $this->assertTrue($query->get_page() == 1);

        $query = $query->set_page(42);
        $this->assertTrue($query->get_page() == 42);
        $query = $query->add_feed('foz');
        $this->assertTrue($query->get_page() == 1);
    }
    public function testResetPageOnNewFacet()
    {
        $query = new AfsQuery();
        $query = $query->set_page(42);
        $query = $query->set_filter('foo', 'bar');
        $this->assertTrue($query->get_page() == 1);

        $query = $query->set_page(42);
        $this->assertTrue($query->get_page() == 42);
        $query = $query->add_filter('foo', 'baz');
        $this->assertTrue($query->get_page() == 1);
    }
    public function testResetPageOnNewRepliesPerPage()
    {
        $query = new AfsQuery();
        $query = $query->set_page(42);
        $query = $query->set_replies(5);
        $this->assertTrue($query->get_page() == 1);
    }
    public function testResetPageOnNewLanguage()
    {
        $query = new AfsQuery();
        $query = $query->set_page(42);
        $query = $query->set_lang('fr');
        $this->assertTrue($query->get_page() == 1);

        $query = $query->set_page(42);
        $query = $query->reset_lang();
        $this->assertTrue($query->get_page() == 1);
    }

    public function testDefaultRepliesPerPage()
    {
        $query = new AfsQuery();
        $this->assertTrue($query->get_replies() == 10);
    }
    public function testSetRepliesPerPage()
    {
        $query = new AfsQuery();
        $query = $query->set_replies(42);
        $this->assertTrue($query->get_replies() == 42);
    }

    public function testSetLanguage()
    {
        $query = new AfsQuery();
        $this->assertFalse($query->has_lang());
        $query = $query->set_lang('en');
        $this->assertTrue($query->get_lang() == 'en');
    }
    public function testSetLanguageWithRegionCode()
    {
        $query = new AfsQuery();
        foreach (array('en-US', 'en_US', 'EN-us') as $lang)
        {
            $query = $query->set_lang($lang);
            $lang = strtolower(strtr($lang, '_', '-'));
            $this->assertTrue($query->get_lang() == $lang);
        }
    }
    public function testResetLanguage()
    {
        $query = new AfsQuery();
        $query = $query->set_lang('en');
        $this->assertTrue($query->get_lang() == 'en');
        $query = $query->reset_lang();
        $this->assertTrue($query->get_lang()->lang == null);
    }
    public function testSetInvalidLanguage()
    {
        $query = new AfsQuery();
        foreach (array('eng', 'en-', 'en_', 'en-U', 'en-USA') as $lang)
        {
            try {
                $query = $query->set_lang($lang);
            } catch (Exception $e) {
                continue;
            }
            $this->fail('Should have failed for invalid language: '. $lang);
        }
    }

    public function testSetSortOrder()
    {
        $query = new AfsQuery();
        $this->assertTrue($query->get_sort() == null);
        $query = $query->set_sort('afs:relevance');
        $this->assertTrue($query->get_sort() == 'afs:relevance');
    }
    public function testResetSortOrder()
    {
        $query = new AfsQuery();
        $query = $query->set_sort('afs:relevance,DESC;afs:words,ASC;afs:foo');
        $this->assertTrue($query->get_sort() == 'afs:relevance,DESC;afs:words,ASC;afs:foo');
        $query = $query->reset_sort();
        $this->assertTrue($query->get_sort() == null);
    }
    public function testCustomSortOrderFacet()
    {
        $query = new AfsQuery();
        $query = $query->set_sort('relevance');
        $this->assertTrue($query->get_sort() == 'relevance');
    }
    public function testInvalidSortOrderOrder()
    {
        return; // TODO activate it when sort order has been updated
        $query = new AfsQuery();
        try {
            $query = $query->set_sort('afs:relevance,DES');
        } catch (Exception $e) {
            return;
        }
        $this->fail('Invalid sort order parameter should have raised an exception!');
    }
    public function testInvalidSortOrderSeparator()
    {
        return; // TODO activate it when sort order has been updated
        $query = new AfsQuery();
        try {
            $query = $query->set_sort('afs:relevance,DESC:afs:relevance');
        } catch (Exception $e) {
            return;
        }
        $this->fail('Invalid sort order parameter should have raised an exception!');
    }

    public function testOriginDefaultValue()
    {
        $query = new AfsQuery();
        $this->assertNull($query->get_from());
    }
    public function testOriginKnownValue()
    {
        $query = new AfsQuery();
        $query = $query->set_from(AfsOrigin::RTE);
        $this->assertEquals(AfsOrigin::RTE, $query->get_from());
    }
    public function testOriginUnknownValue()
    {
        $query = new AfsQuery();
        try {
            $query = $query->set_from('UnknownValue');
        } catch (Exception $e) {
            return;
        }
        $this->fail('Unknown query origin value should have raised exception!');
    }
    public function testOriginAutoSetForQuery()
    {
        $query = new AfsQuery();
        $query = $query->set_query('foo');
        $this->assertEquals(AfsOrigin::SEARCHBOX, $query->get_from());
    }
    public function testOriginAutoSetForFilters()
    {
        $query = new AfsQuery();
        $query = $query->set_filter('foo', 'bar');
        $this->assertEquals(AfsOrigin::FACET, $query->get_from());
    }
    public function testOriginAutoSetForPager()
    {
        $query = new AfsQuery();
        $query = $query->set_page(42);
        $this->assertEquals(AfsOrigin::PAGER, $query->get_from());
    }

    public function testNoUserId()
    {
        $query = new AfsQuery();
        $this->assertFalse($query->has_user_id());
        $this->assertNull($query->get_user_id());
    }
    public function testUserId()
    {
        $query = new AfsQuery();
        $query = $query->set_user_id('foo');
        $this->assertTrue($query->has_user_id());
        $this->assertEquals('foo', $query->get_user_id());
    }

    public function testNoSessionId()
    {
        $query = new AfsQuery();
        $this->assertFalse($query->has_session_id());
        $this->assertNull($query->get_session_id());
    }
    public function testSessionId()
    {
        $query = new AfsQuery();
        $query = $query->set_session_id('foo');
        $this->assertTrue($query->has_session_id());
        $this->assertEquals('foo', $query->get_session_id());
    }
    public function testResetSessionIdWhenUserIdIsModified()
    {
        $query = new AfsQuery();
        $query = $query->set_user_id('foo')->set_session_id('bar');
        $this->assertEquals('foo', $query->get_user_id());
        $this->assertEquals('bar', $query->get_session_id());

        $query = $query->set_user_id('baz');
        $this->assertEquals('baz', $query->get_user_id());
        $this->assertNull($query->get_session_id());
    }

    public function testPreserveSessionIdWhenUserIdIsNotModified()
    {
        $query = new AfsQuery();
        $query = $query->set_user_id('foo')->set_session_id('bar');
        $this->assertEquals('foo', $query->get_user_id());
        $this->assertEquals('bar', $query->get_session_id());

        $query = $query->set_user_id('foo');
        $this->assertEquals('foo', $query->get_user_id());
        $this->assertEquals('bar', $query->get_session_id());
    }

    public function testNoUpdateUserAndSession()
    {
        $query = new AfsQuery();
        $query = $query->set_user_id('foo')->set_session_id('bar');
        $query->update_user_and_session_id('bla', 'blo');
        $this->assertEquals('foo', $query->get_user_id());
        $this->assertEquals('bar', $query->get_session_id());
    }

    public function testUpdateUserAndSession()
    {
        $query = new AfsQuery();
        $query->update_user_and_session_id('bla', 'blo');
        $this->assertEquals('bla', $query->get_user_id());
        $this->assertEquals('blo', $query->get_session_id());
    }

    public function testNoLog()
    {
        $query = new AfsQuery();
        $this->assertEquals(0, count($query->get_logs()));
    }

    public function testSomeLogs()
    {
        $query = new AfsQuery();
        $query->add_log('foo');
        $query->add_log('bar');
        $logs = $query->get_logs();
        $this->assertEquals(2, count($logs));
        $this->assertEquals('foo', $logs[0]);
        $this->assertEquals('bar', $logs[1]);
    }

    public function testNoKey()
    {
        $query = new AfsQuery();
        $this->assertFalse($query->has_key());
        $this->assertEquals(null, $query->get_key());
    }

    public function testKey()
    {
        $query = new AfsQuery();
        $query->set_key('test');
        $this->assertTrue($query->has_key());
        $this->assertEquals($query->get_key(), 'test');
    }

    public function testCloneQuery()
    {
        $query = new AfsQuery();
        $query = $query->set_query('query');
        $query = $query->add_filter('foo', 'bar');
        $query = $query->add_filter('foo', 'baz');
        $query = $query->add_filter('fox', 'bat');
        $query = $query->add_filter('fox', 'bas');
        $query = $query->add_feed('feed');
        $query = $query->add_feed('food');
        $query = $query->set_replies(666);
        $query = $query->set_lang('en');
        $query = $query->set_sort('afs:weight,ASC;afs:foo;afs:BAR,DESC');
        $query = $query->set_page(42);
        $query = $query->set_from(AfsOrigin::SEARCHBOX);
        $query = $query->add_log('loggy');
        $clone = new AfsQuery($query);
        $this->assertTrue($clone->get_query('query') == 'query');
        $this->assertTrue($clone->has_filter('foo', 'bar'));
        $this->assertTrue($clone->has_filter('foo', 'baz'));
        $this->assertTrue($clone->has_filter('fox', 'bat'));
        $this->assertTrue($clone->has_filter('fox', 'bas'));
        $this->assertTrue(in_array('feed', $clone->get_feeds()));
        $this->assertTrue(in_array('food', $clone->get_feeds()));
        $this->assertTrue($clone->get_page() == 42);
        $this->assertTrue($clone->get_replies() == 666);
        $this->assertTrue($clone->get_lang() == 'en');
        $this->assertTrue($clone->get_sort() == 'afs:weight,ASC;afs:foo;afs:BAR,DESC');
        $this->assertEquals(AfsOrigin::SEARCHBOX, $clone->get_from());
        $logs = $clone->get_logs();
        $this->assertEquals(1, count($logs));
        $this->assertEquals('loggy', $logs[0]);
    }

    public function testRetrieveParametersArray()
    {
        $query = new AfsQuery();
        $query = $query->set_query('query');

        $query = $query->add_filter('foo', 'bar');
        $query = $query->add_filter('foo', 'baz');
        $query = $query->add_filter('fox', 'bat');
        $query = $query->add_filter('fox', 'bas');

        $query = $query->add_feed('feed');
        $query = $query->add_feed('food');

        $query = $query->set_replies(666);

        $query = $query->set_lang('en');

        $query = $query->set_sort('afs:weight,ASC;afs:foo;afs:BAR,DESC');

        $query = $query->set_page(42);

        $query = $query->set_from(AfsOrigin::CONCEPT);

        $query = $query->add_log('loggy');
        $query = $query->add_log('loggo');

        $result = $query->get_parameters();
        $this->assertTrue(array_key_exists('query', $result));
        $this->assertTrue($result['query'] == 'query');

        $this->assertTrue(array_key_exists('filter', $result));
        $this->assertTrue(array_key_exists('foo', $result['filter']));
        $this->assertTrue(in_array('bar', $result['filter']['foo']));
        $this->assertTrue(in_array('baz', $result['filter']['foo']));
        $this->assertTrue(array_key_exists('fox', $result['filter']));
        $this->assertTrue(in_array('bat', $result['filter']['fox']));
        $this->assertTrue(in_array('bas', $result['filter']['fox']));

        $this->assertTrue(array_key_exists('feed', $result));
        $this->assertTrue(in_array('feed', $result['feed']));
        $this->assertTrue(in_array('food', $result['feed']));

        $this->assertTrue(array_key_exists('replies', $result));
        $this->assertTrue($result['replies'] == 666);

        $this->assertTrue(array_key_exists('lang', $result));
        $this->assertTrue($result['lang'] == 'en');

        $this->assertTrue(array_key_exists('sort', $result));
        $this->assertTrue($result['sort'] == 'afs:weight,ASC;afs:foo;afs:BAR,DESC');

        $this->assertTrue(array_key_exists('page', $result));
        $this->assertTrue($result['page'] == 42);

        $this->assertTrue(array_key_exists('from', $result));
        $this->assertEquals(AfsOrigin::CONCEPT, $result['from']);

        $this->assertTrue(array_key_exists('log', $result));
        $this->assertEquals('loggy', $result['log'][0]);
        $this->assertEquals('loggo', $result['log'][1]);
    }

    public function testInitializeWithArray()
    {
        $query = AfsQuery::create_from_parameters(array(
            'page' => 42,
            'query' => 'query',
            'filter' => array('foo' => array('bar', 'baz'),
                              'fox' => array('bat', 'bas')),
            'feed' => array('feed', 'food'),
            'replies' => 666,
            'lang' => 'en',
            'sort' => 'afs:weight,ASC;afs:foo;afs:BAR,DESC',
            'from' => 'PAGER',
            'log' => array('loggy', 'loggo')));

        $this->assertTrue($query->has_query());
        $this->assertTrue($query->get_query() == 'query');

        $this->assertTrue(in_array('foo', $query->get_filters()));
        $this->assertTrue(in_array('bar', $query->get_filter_values('foo')));
        $this->assertTrue(in_array('baz', $query->get_filter_values('foo')));
        $this->assertTrue(in_array('fox', $query->get_filters()));
        $this->assertTrue(in_array('bat', $query->get_filter_values('fox')));
        $this->assertTrue(in_array('bas', $query->get_filter_values('fox')));

        $this->assertTrue($query->has_feed());
        $this->assertTrue(in_array('feed', $query->get_feeds()));
        $this->assertTrue(in_array('food', $query->get_feeds()));

        $this->assertTrue($query->has_replies());
        $this->assertTrue($query->get_replies() == 666);

        $this->assertTrue($query->has_lang());
        $this->assertTrue($query->get_lang() == 'en');

        $this->assertTrue($query->has_sort());
        $this->assertTrue($query->get_sort() == 'afs:weight,ASC;afs:foo;afs:BAR,DESC');

        $this->assertTrue($query->has_page());
        $this->assertTrue($query->get_page() == 42);

        $this->assertEquals(AfsOrigin::PAGER, $query->get_from());

        $logs = $query->get_logs();
        $this->assertEquals(2, count($logs));
        $this->assertEquals('loggy', $logs[0]);
        $this->assertEquals('loggo', $logs[1]);
    }
}


