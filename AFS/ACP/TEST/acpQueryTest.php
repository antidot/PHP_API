<?php ob_start();
require_once "AFS/ACP/afs_acp_query.php";


class AcpQueryTest extends PHPUnit_Framework_TestCase
{
    public function testSetQuery()
    {
        $query = new AfsAcpQuery();
        $query = $query->set_query('foo');
        $this->assertTrue($query->get_query() == 'foo');
    }
    public function testSetNewQueryValue()
    {
        $query = new AfsAcpQuery();
        $query = $query->set_query('foo');
        $query = $query->set_query('bar');
        $this->assertFalse($query->get_query() == 'foo');
        $this->assertTrue($query->get_query() == 'bar');
    }

    public function testHasNoQuery()
    {
        $query = new AfsAcpQuery();
        $this->assertFalse($query->has_query());
    }
    public function testHasQuery()
    {
        $query = new AfsAcpQuery();
        $query = $query->set_query('foo');
        $this->assertTrue($query->has_query());
    }

    public function testHasNoFeedSet()
    {
        $query = new AfsAcpQuery();
        $this->assertFalse($query->has_feed());
    }
    public function testHasFeedName()
    {
        $query = new AfsAcpQuery();
        $query = $query->set_feed('foo');
        $this->assertTrue($query->has_feed());
        $this->assertTrue(in_array('foo', $query->get_feeds()));
    }
    public function testHasFeedNames()
    {
        $query = new AfsAcpQuery();
        $query = $query->add_feed('foo');
        $query = $query->add_feed('bar');
        $this->assertTrue($query->has_feed());
        $this->assertTrue(in_array('foo', $query->get_feeds()));
        $this->assertTrue(in_array('bar', $query->get_feeds()));
    }
    public function testResetFeedName()
    {
        $query = new AfsAcpQuery();
        $query = $query->add_feed('foo');
        $query = $query->add_feed('bar');
        $query = $query->set_feed('baz');
        $this->assertTrue($query->has_feed());
        $this->assertFalse(in_array('foo', $query->get_feeds()));
        $this->assertFalse(in_array('bar', $query->get_feeds()));
        $this->assertTrue(in_array('baz', $query->get_feeds()));
    }

    public function testDefaultNumberOfReplies()
    {
        $query = new AfsAcpQuery();
        $this->assertTrue($query->get_replies() == 10);
    }
    public function testSetNumberOfReplies()
    {
        $query = new AfsAcpQuery();
        $query = $query->set_replies(42);
        $this->assertTrue($query->get_replies() == 42);
    }

    public function testNoUserId()
    {
        $query = new AfsAcpQuery();
        $id = $query->get_user_id();
        $this->assertFalse(empty($id));
    }
    public function testUserId()
    {
        $query = new AfsAcpQuery();
        $query = $query->set_user_id('foo');
        $this->assertEquals('foo', $query->get_user_id());
    }

    public function testNoSessionId()
    {
        $query = new AfsAcpQuery();
        $id = $query->get_session_id();
        $this->assertFalse(empty($id));
    }
    public function testSessionId()
    {
        $query = new AfsAcpQuery();
        $query = $query->set_session_id('foo');
        $this->assertEquals('foo', $query->get_session_id());
    }

    public function testUserIdInitFromManager()
    {
        $name = 'MyUserCookie';
        $_COOKIE[$name] = 'foo';
        $mgr = new AfsUserSessionManager($name);
        $query = new AfsAcpQuery();
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
        $query = new AfsAcpQuery();
        $user_id = $query->get_user_id();
        $session_id = $query->get_session_id();
        $query = $query->initialize_user_and_session_id($mgr);
        $this->assertEquals($user_id, $query->get_user_id());
        $this->assertFalse($session_id == $query->get_session_id());
    }

    public function testNoLog()
    {
        $query = new AfsAcpQuery();
        $this->assertEquals(0, count($query->get_logs()));
    }

    public function testSomeLogs()
    {
        $query = new AfsAcpQuery();
        $query->add_log('foo');
        $query->add_log('bar');
        $logs = $query->get_logs();
        $this->assertEquals(2, count($logs));
        $this->assertEquals('foo', $logs[0]);
        $this->assertEquals('bar', $logs[1]);
    }

    public function testNoKey()
    {
        $query = new AfsAcpQuery();
        $this->assertFalse($query->has_key());
        $this->assertEquals(null, $query->get_key());
    }

    public function testKey()
    {
        $query = new AfsAcpQuery();
        $query->set_key('test');
        $this->assertTrue($query->has_key());
        $this->assertEquals($query->get_key(), 'test');
    }

    public function testCloneQuery()
    {
        $query = new AfsAcpQuery();
        $query = $query->set_query('query')
                       ->add_feed('feed')
                       ->add_feed('food')
                       ->set_replies(666)
                       ->add_log('loggy');
        $clone = new AfsAcpQuery($query);
        $this->assertTrue($clone->get_query('query') == 'query');
        $this->assertTrue(in_array('food', $clone->get_feeds()));
        $this->assertTrue($clone->get_replies() == 666);

        $logs = $clone->get_logs();
        $this->assertEquals(1, count($logs));
        $this->assertEquals('loggy', $logs[0]);
    }

    public function testRetrieveParametersArray()
    {
        $query = new AfsAcpQuery();
        $query = $query->set_query('query');

        $query = $query->add_feed('feed');
        $query = $query->add_feed('food');

        $query = $query->set_replies(666);

        $query = $query->add_log('loggy');
        $query = $query->add_log('loggo');

        $result = $query->get_parameters();
        $this->assertTrue(array_key_exists('query', $result));
        $this->assertTrue($result['query'] == 'query');

        $this->assertTrue(array_key_exists('feed', $result));
        $this->assertTrue(in_array('feed', $result['feed']));
        $this->assertTrue(in_array('food', $result['feed']));

        $this->assertTrue(array_key_exists('replies', $result));
        $this->assertTrue($result['replies'] == 666);

        $this->assertTrue(array_key_exists('log', $result));
        $this->assertEquals('loggy', $result['log'][0]);
        $this->assertEquals('loggo', $result['log'][1]);
    }
}

