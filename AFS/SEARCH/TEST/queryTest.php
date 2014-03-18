<?php ob_start();
require_once 'AFS/SEARCH/afs_query.php';
require_once 'AFS/SEARCH/afs_interval_helper.php';
require_once 'AFS/SEARCH/FILTER/afs_filter.php';


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
    public function testAddFilterArrayValues()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', array('bar', 'baz'));
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
    public function testOverwriteMultipleValuesForFilter()
    {
        $query = new AfsQuery();
        $query = $query->add_filter('foo', 'bar');
        $query = $query->add_filter('foo', 'baz');
        $query = $query->set_filter('foo', array('for', 'foz'));
        $this->assertFalse($query->has_filter('foo', 'bar'));
        $this->assertFalse($query->has_filter('foo', 'baz'));
        $this->assertTrue($query->has_filter('foo', 'foz'));
        $this->assertTrue($query->has_filter('foo', 'foz'));
    }

    public function testSetIntervalValueToFilter()
    {
        $query = new AfsQuery();
        $query = $query->set_filter('foo', AfsIntervalHelper::create(42, 666));
        $this->assertTrue($query->has_filter('foo', '[42 .. 666]'));
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

    public function testNoAdvancedFilter()
    {
        $query = new AfsQuery();
        $this->assertFalse($query->has_advanced_filter());
    }
    public function testOneAdvancedFilter()
    {
        $query = new AfsQuery();
        $query = $query->set_advanced_filter(filter('FOO')->less->value('bar'));
        $this->assertTrue($query->has_advanced_filter());
        $advanced_filters = $query->get_advanced_filters();
        $this->assertEquals(1, count($advanced_filters));
        $this->assertEquals('FOO<bar', $advanced_filters[0]);
    }
    public function testMultipleAdvancedFilter()
    {
        $query = new AfsQuery();
        $query = $query->add_advanced_filter(filter('FOO')->less->value('bar'))
            ->add_advanced_filter(filter('FOZ')->greater->value('baz'));
        $this->assertTrue($query->has_advanced_filter());
        $advanced_filters = $query->get_advanced_filters();
        $this->assertEquals(2, count($advanced_filters));
        $this->assertEquals('FOO<bar', $advanced_filters[0]);
        $this->assertEquals('FOZ>baz', $advanced_filters[1]);
    }
    public function testOverrideAdvancedFilter()
    {
        $query = new AfsQuery();
        $query = $query->set_advanced_filter(filter('FOO')->less->value('bar'));
        $query = $query->set_advanced_filter(filter('FOZ')->less->value('baz'));
        $this->assertTrue($query->has_advanced_filter());
        $advanced_filters = $query->get_advanced_filters();
        $this->assertEquals(1, count($advanced_filters));
        $this->assertEquals('FOZ<baz', $advanced_filters[0]);
    }
    public function testResetAdvancedFilter()
    {
        $query = new AfsQuery();
        $query = $query->set_advanced_filter(filter('FOO')->less->value('bar'));
        $query = $query->reset_advanced_filter();
        $this->assertFalse($query->has_advanced_filter());
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
        $this->assertEquals(42, $query->get_page());

        $query = $query->reset_lang();
        $this->assertEquals(42, $query->get_page());
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
        $this->assertFalse($query->has_sort());
        $this->assertFalse($query->has_sort('foo'));
        $query = $query->set_sort('afs:relevance');
        $this->assertTrue($query->has_sort());
        $this->assertTrue($query->has_sort('afs:relevance'));
        $this->assertEquals(AfsSortOrder::DESC, $query->get_sort_order('afs:relevance'));
    }
    public function testResetSortOrder()
    {
        $query = new AfsQuery();
        $query = $query->set_sort('afs:relevance', AfsSortOrder::DESC)
            ->add_sort('afs:words', AfsSortOrder::ASC)
            ->add_sort('foo');
        $this->assertTrue($query->has_sort('afs:relevance'));
        $this->assertEquals(AfsSortOrder::DESC, $query->get_sort_order('afs:relevance'));
        $this->assertTrue($query->has_sort('afs:words'));
        $this->assertEquals(AfsSortOrder::ASC, $query->get_sort_order('afs:words'));
        $this->assertTrue($query->has_sort('foo'));
        $this->assertEquals(AfsSortOrder::DESC, $query->get_sort_order('foo'));
        $query = $query->reset_sort();
        $this->assertFalse($query->has_sort());
    }
    public function testCustomSortOrderFacet()
    {
        $query = new AfsQuery();
        $query = $query->set_sort('relevance');
        $this->assertTrue($query->has_sort('relevance'));
        $this->assertEquals(AfsSortOrder::DESC, $query->get_sort_order('relevance'));
    }
    public function testInvalidSortOrderOrder()
    {
        $query = new AfsQuery();
        try {
            $query = $query->set_sort('afs:relevance', 'DES');
        } catch (Exception $e) {
            return;
        }
        $this->fail('Invalid sort order parameter should have raised an exception!');
    }

    public function testNoCluster()
    {
        $query = new AfsQuery();
        $this->assertFalse($query->has_cluster());
        $this->assertFalse($query->has_max_clusters());
        $this->assertFalse($query->has_overspill());
        $this->assertEquals(null, $query->get_count_mode());
    }
    public function testSimpleCluster()
    {
        $query = new AfsQuery();
        $query = $query->set_cluster('Foo', 3);
        $this->assertTrue($query->has_cluster());
        $this->assertEquals('Foo', $query->get_cluster_id());
        $this->assertEquals(3, $query->get_nb_replies_per_cluster());
        $this->assertFalse($query->has_max_clusters());
        $this->assertFalse($query->has_overspill());
        $this->assertEquals(null, $query->get_count_mode());
    }
    public function testClusterLimit()
    {
        $query = new AfsQuery();
        $query = $query->set_cluster('Foo', 3)->set_max_clusters(42);
        $this->assertTrue($query->has_cluster());
        $this->assertTrue($query->has_max_clusters());
        $this->assertEquals(42, $query->get_max_clusters());
        $this->assertFalse($query->has_overspill());
        $this->assertEquals(null, $query->get_count_mode());
    }
    public function testClusterOverspill()
    {
        $query = new AfsQuery();
        $query = $query->set_cluster('Foo', 3)->set_overspill();
        $this->assertTrue($query->has_cluster());
        $this->assertFalse($query->has_max_clusters());
        $this->assertTrue($query->has_overspill());
        $this->assertEquals(null, $query->get_count_mode());

        $query = $query->set_overspill(false);
        $this->assertFalse($query->has_overspill());
    }
    public function testClusterCountMode()
    {
        $query = new AfsQuery();
        $query = $query->set_cluster('Foo', 3)->set_count(AfsCount::CLUSTERS);
        $this->assertTrue($query->has_cluster());
        $this->assertFalse($query->has_max_clusters());
        $this->assertFalse($query->has_overspill());
        $this->assertEquals(AfsCount::CLUSTERS, $query->get_count_mode());
    }
    public function testClusterDocumentsCountMode()
    {
        $query = new AfsQuery();
        $query = $query->set_cluster('Foo', 3)->set_count(AfsCount::DOCUMENTS);
        $this->assertTrue($query->has_cluster());
        $this->assertFalse($query->has_max_clusters());
        $this->assertFalse($query->has_overspill());
        $this->assertEquals(AfsCount::DOCUMENTS, $query->get_count_mode());
    }
    public function testUnsetCluster()
    {
        $query = new AfsQuery();
        $query = $query->set_cluster('Foo', 3)
            ->set_overspill()
            ->set_max_clusters(42)
            ->set_count(AfsCount::CLUSTERS);
        $this->assertTrue($query->has_cluster());
        $this->assertTrue($query->has_max_clusters());
        $this->assertTrue($query->has_overspill());
        $this->assertEquals(AfsCount::CLUSTERS, $query->get_count_mode());

        $query = $query->unset_cluster();
        $this->assertFalse($query->has_cluster());
        $this->assertFalse($query->has_max_clusters());
        $this->assertFalse($query->has_overspill());
        $this->assertEquals(null, $query->get_count_mode());
    }
    public function testUninitializedClusterFailsOnMaxClusters()
    {
        $query = new AfsQuery();
        try {
            $query->set_max_clusters(42);
            $this->fail('Setting Max Clusters on uninitialized cluster should have failed!');
        } catch (AfsClusterException $e) {}
    }
    public function testUninitializedClusterFailsOnCount()
    {
        $query = new AfsQuery();
        try {
            $query->set_count(AfsCount::CLUSTERS);
            $this->fail('Setting cluster count mode on uninitialized cluster should have failed!');
        } catch (AfsClusterException $e) {}
    }
    public function testUninitializedClusterFailsOnOverspill()
    {
        $query = new AfsQuery();
        try {
            $query->set_overspill();
            $this->fail('Setting overspill on uninitialized cluster should have failed!');
        } catch (AfsClusterException $e) {}
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
        $query = $query->auto_set_from()->set_query('foo');
        $this->assertEquals(AfsOrigin::SEARCHBOX, $query->get_from());
    }
    public function testOriginAutoSetForFilters()
    {
        $query = new AfsQuery();
        $query = $query->auto_set_from()->set_filter('foo', 'bar');
        $this->assertEquals(AfsOrigin::FACET, $query->get_from());
    }
    public function testOriginAutoSetForPager()
    {
        $query = new AfsQuery();
        $query = $query->auto_set_from()->set_page(42);
        $this->assertEquals(AfsOrigin::PAGER, $query->get_from());
    }
    public function testOriginNotAutoSet()
    {
        $query = new AfsQuery();
        $query = $query->set_query('query');
        $this->assertTrue(is_null($query->get_from()));
    }

    public function testNoUserId()
    {
        $query = new AfsQuery();
        $id = $query->get_user_id();
        $this->assertFalse(empty($id));
    }
    public function testUserId()
    {
        $query = new AfsQuery();
        $query = $query->set_user_id('foo');
        $this->assertEquals('foo', $query->get_user_id());
    }

    public function testNoSessionId()
    {
        $query = new AfsQuery();
        $id = $query->get_session_id();
        $this->assertFalse(empty($id));
    }
    public function testSessionId()
    {
        $query = new AfsQuery();
        $query = $query->set_session_id('foo');
        $this->assertEquals('foo', $query->get_session_id());
    }

    public function testUserIdInitFromManager()
    {
        $name = 'MyUserCookie';
        $_COOKIE[$name] = 'foo';
        $mgr = new AfsUserSessionManager($name);
        $query = new AfsQuery();
        $user_id = $query->get_user_id();
        $session_id = $query->get_session_id();
        $query = $query->initialize_user_and_session_id($mgr);
        $this->assertFalse($user_id == $query->get_user_id());
        $this->assertEquals($session_id, $query->get_session_id());
    }
    public function testSessionIdInitFromManager()
    {
        $name = 'MySessionCookie';
        $_COOKIE[$name] = 'bar';
        $mgr = new AfsUserSessionManager('blabla', $name);
        $query = new AfsQuery();
        $user_id = $query->get_user_id();
        $session_id = $query->get_session_id();
        $query = $query->initialize_user_and_session_id($mgr);
        $this->assertEquals($user_id, $query->get_user_id());
        $this->assertFalse($session_id == $query->get_session_id());
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
        $query = $query->set_query('query')
                       ->add_filter('foo', 'bar')
                       ->add_filter('foo', 'baz')
                       ->add_filter('fox', 'bat')
                       ->add_filter('fox', 'bas')
                       ->add_feed('feed')
                       ->add_feed('food')
                       ->set_replies(666)
                       ->set_lang('en')
                       ->set_sort(AfsSortBuiltins::WEIGHT, AfsSortOrder::ASC)
                       ->add_sort('foo')
                       ->add_sort('BAR')
                       ->set_cluster('CLUSTER', 42666)
                       ->set_max_clusters(66642)
                       ->set_overspill()
                       ->set_count(AfsCount::CLUSTERS)
                       ->set_page(42)
                       ->set_from(AfsOrigin::SEARCHBOX)
                       ->add_log('loggy')
                       ->add_advanced_filter(filter('foo')->greater->value(666))
                       ->auto_set_from();
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
        $this->assertEquals(AfsSortOrder::ASC, $clone->get_sort_order('afs:weight'));
        $this->assertTrue($clone->has_sort('foo'));
        $this->assertEquals(AfsSortOrder::DESC, $clone->get_sort_order('foo'));
        $this->assertTrue($clone->has_sort('BAR'));
        $this->assertEquals(AfsSortOrder::DESC, $clone->get_sort_order('BAR'));

        $this->assertTrue($clone->has_cluster());
        $this->assertEquals('CLUSTER', $clone->get_cluster_id());
        $this->assertEquals(42666, $clone->get_nb_replies_per_cluster());
        $this->assertTrue($clone->has_max_clusters());
        $this->assertEquals(66642, $clone->get_max_clusters());
        $this->assertTrue($clone->has_overspill());
        $this->assertEquals(AfsCount::CLUSTERS, $clone->get_count_mode());

        $this->assertEquals(AfsOrigin::SEARCHBOX, $clone->get_from());
        $logs = $clone->get_logs();
        $this->assertEquals(1, count($logs));
        $this->assertEquals('loggy', $logs[0]);

        $this->assertTrue($clone->has_advanced_filter());
        $adv_filters = $clone->get_advanced_filters();
        $this->assertEquals('foo>666', $adv_filters[0]);

        // Need to call specific method to check that auto set from is active
        $clone = $clone->add_filter('youhou', 'bloublou');
        $this->assertEquals(AfsOrigin::FACET, $clone->get_from());
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

        $query = $query->set_sort(AfsSortBuiltins::WEIGHT, AfsSortOrder::ASC)
                       ->add_sort('foo')
                       ->add_sort('BAR');

        $query = $query->set_cluster('CLUSTER', 666)
                       ->set_max_clusters(3)
                       ->set_overspill()
                       ->set_count(AfsCount::CLUSTERS);

        $query = $query->set_advanced_filter(filter('FOO')->less_equal->value(42));

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
        $kv = each($result['sort']);
        $this->assertEquals('afs:weight', $kv[0]);
        $this->assertEquals('ASC', $kv[1]);
        $kv = each($result['sort']);
        $this->assertEquals('foo', $kv[0]);
        $this->assertEquals('DESC', $kv[1]);
        $kv = each($result['sort']);
        $this->assertEquals('BAR', $kv[0]);
        $this->assertEquals('DESC', $kv[1]);

        $this->assertTrue(array_key_exists('cluster', $result));
        $this->assertEquals('CLUSTER,666', $result['cluster']);
        $this->assertTrue(array_key_exists('maxClusters', $result));
        $this->assertEquals(3, $result['maxClusters']);
        $this->assertTrue(array_key_exists('overspill', $result));
        $this->assertEquals('true', $result['overspill']);
        $this->assertTrue(array_key_exists('count', $result));
        $this->assertEquals('clusters', $result['count']);

        $this->assertTrue(array_key_exists('advancedFilter', $result));
        $this->assertEquals('FOO<=42', $result['advancedFilter'][0]);

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
            'sort' => array('afs:weight' => 'ASC',
                            'foo' => 'DESC',
                            'BAR' => 'DESC'),
            'cluster' => 'CLUSTER,6',
            'maxClusters' => '666',
            'overspill' => 'true',
            'count' => 'clusters',
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
        $this->assertTrue($query->has_sort('afs:weight'));
        $this->assertEquals(AfsSortOrder::ASC, $query->get_sort_order('afs:weight'));
        $this->assertTrue($query->has_sort('foo'));
        $this->assertEquals(AfsSortOrder::DESC, $query->get_sort_order('foo'));
        $this->assertTrue($query->has_sort('BAR'));
        $this->assertEquals(AfsSortOrder::DESC, $query->get_sort_order('BAR'));

        $this->assertEquals('CLUSTER', $query->get_cluster_id());
        $this->assertEquals(6, $query->get_nb_replies_per_cluster());
        $this->assertEquals(666, $query->get_max_clusters());
        $this->assertTrue($query->has_overspill());
        $this->assertEquals(AfsCount::CLUSTERS, $query->get_count_mode());

        $this->assertTrue($query->has_page());
        $this->assertTrue($query->get_page() == 42);

        $this->assertEquals(AfsOrigin::PAGER, $query->get_from());

        $logs = $query->get_logs();
        $this->assertEquals(2, count($logs));
        $this->assertEquals('loggy', $logs[0]);
        $this->assertEquals('loggo', $logs[1]);
    }

    public function testInitializeWithArrayWithoutOverspill()
    {
        $query = AfsQuery::create_from_parameters(array(
            'cluster' => 'CLUSTER,6',
            'maxClusters' => '666',
            'count' => 'documents'));

        $this->assertEquals('CLUSTER', $query->get_cluster_id());
        $this->assertEquals(6, $query->get_nb_replies_per_cluster());
        $this->assertEquals(666, $query->get_max_clusters());
        $this->assertFalse($query->has_overspill());
        $this->assertEquals(AfsCount::DOCUMENTS, $query->get_count_mode());
    }

    public function testInitializeWithUnknownValueInArray()
    {
        $query = AfsQuery::create_from_parameters(array(
            'X' => '42',
            'Y' => '666'));
        $this->assertEquals(0, count($query->get_filters()));
        $this->assertFalse($query->has_query());
    }

    public function testRetrieveFacetManager()
    {
        $query = new AfsQuery();
        $mgr = $query->get_facet_manager();
        $this->assertNotNull($mgr);
        $facets = $mgr->get_facets();
        $this->assertTrue(empty($facets));
    }

    public function testRetrieveFacetManagerAndUpdate()
    {
        $query = new AfsQuery();
        $mgr = $query->get_facet_manager();
        $this->assertNotNull($mgr);
        $facets = $mgr->get_facets();
        $this->assertTrue(empty($facets));
        $mgr->add_facet(new AfsFacet('FOO', AfsFacetType::STRING_TYPE));

        $mgr = $query->get_facet_manager();
        $this->assertNotNull($mgr);
        $facets = $mgr->get_facets();
        $this->assertFalse(empty($facets));
        $this->assertTrue(array_key_exists('FOO', $facets));
    }

    public function testDefaultFacetOptionMultiValuedMode()
    {
        $query = new AfsQuery();
        $query = $query->set_default_multi_selection_facets();
        $facet_mgr = $query->get_facet_manager();
        $this->assertEquals(AfsFacetMode::OR_MODE, $facet_mgr->get_default_facets_mode());
    }
    public function testDefaultFacetOptionSingleValuedMode()
    {
        $query = new AfsQuery();
        $query = $query->set_default_mono_selection_facets();
        $facet_mgr = $query->get_facet_manager();
        $this->assertEquals(AfsFacetMode::SINGLE_MODE, $facet_mgr->get_default_facets_mode());
    }
    public function testFacetMultiValued()
    {
        $query = new AfsQuery();
        $query = $query->set_multi_selection_facets('FOO');
        $facet_mgr = $query->get_facet_manager();
        $this->assertTrue($facet_mgr->has_facet('FOO'));
        $facet = $facet_mgr->get_facet('FOO');
        $this->assertTrue($facet->has_or_mode());
    }
    public function testFacetsMultiValuedAsArray()
    {
        $query = new AfsQuery();
        $facets = array('FOO', 'BAR');
        $query = $query->set_multi_selection_facets($facets);
        $facet_mgr = $query->get_facet_manager();
        foreach ($facets as $facet) {
            $this->assertTrue($facet_mgr->has_facet($facet));
            $facet = $facet_mgr->get_facet($facet);
            $this->assertTrue($facet->has_or_mode());
        }
    }
    public function testFacetsMultiValuedAsList()
    {
        $query = new AfsQuery();
        $query = $query->set_multi_selection_facets('FOO', 'BAR');
        $facet_mgr = $query->get_facet_manager();
        foreach (array('FOO', 'BAR') as $facet) {
            $this->assertTrue($facet_mgr->has_facet($facet));
            $facet = $facet_mgr->get_facet($facet);
            $this->assertTrue($facet->has_or_mode());
        }
    }
    public function testFacetSingleValuedAsArray()
    {
        $query = new AfsQuery();
        $query = $query->set_mono_selection_facets('FOO');
        $facet_mgr = $query->get_facet_manager();
        $this->assertTrue($facet_mgr->has_facet('FOO'));
        $facet = $facet_mgr->get_facet('FOO');
        $this->assertTrue($facet->has_single_mode());
    }
    public function testFacetsSingleValuedAsList()
    {
        $query = new AfsQuery();
        $facets = array('FOO', 'BAR');
        $query = $query->set_mono_selection_facets($facets);
        $facet_mgr = $query->get_facet_manager();
        foreach ($facets as $facet) {
            $this->assertTrue($facet_mgr->has_facet($facet));
            $facet = $facet_mgr->get_facet($facet);
            $this->assertTrue($facet->has_single_mode());
        }
    }
    public function testFacetsSingleValued()
    {
        $query = new AfsQuery();
        
        $query = $query->set_mono_selection_facets('FOO', 'BAR');
        $facet_mgr = $query->get_facet_manager();
        foreach (array('FOO', 'BAR') as $facet) {
            $this->assertTrue($facet_mgr->has_facet($facet));
            $facet = $facet_mgr->get_facet($facet);
            $this->assertTrue($facet->has_single_mode());
        }
    }

    public function testSmoothFacetSortOrder()
    {
        $query = new AfsQuery();
        $this->assertFalse($query->get_facet_manager()->is_facet_order_strict());
    }
    public function testStrictFacetSortOrder()
    {
        $query = new AfsQuery();
        $query = $query->set_facet_order(array('foo', 'bar'), AfsFacetOrder::STRICT);
        $this->assertTrue($query->get_facet_manager()->is_facet_order_strict());
    }
}


