<?php
require_once "afs_user_session_manager.php";

class UserSessionTest extends PHPUnit_Framework_TestCase
{
    // Cannot test setcookie due to other tests which have already written on
    // the output!

    public function testRetrieveNoUserId()
    {
        $mgr = new AfsUserSessionManager();
        $this->assertNull($mgr->get_user_id());
    }
    public function testRetrieveUserId()
    {
        global $_COOKIE;
        $_COOKIE['AfsUserId'] = 'youhou';
        $mgr = new AfsUserSessionManager();
        $this->assertEquals('youhou', $mgr->get_user_id());
    }
    public function testRetrieveUndefinedSpecificUserIdName()
    {
        global $_COOKIE;
        $_COOKIE['AfsUserId'] = 'youhou';
        $mgr = new AfsUserSessionManager('myName');
        $this->assertNull($mgr->get_user_id());
    }
    public function testRetrieveSpecificUserIdName()
    {
        global $_COOKIE;
        $_COOKIE['AfsUserId'] = 'youhou';
        $_COOKIE['myName'] = 'wouhahaha';
        $mgr = new AfsUserSessionManager('myName');
        $this->assertEquals('wouhahaha', $mgr->get_user_id());
    }

    public function testRetrieveNoSessionId()
    {
        $mgr = new AfsUserSessionManager();
        $this->assertNull($mgr->get_session_id());
    }
    public function testRetrieveSessionId()
    {
        global $_COOKIE;
        $_COOKIE['AfsSessionId'] = 'youhou';
        $mgr = new AfsUserSessionManager();
        $this->assertEquals('youhou', $mgr->get_session_id());
    }
    public function testRetrieveUndefinedSpecificSessionIdName()
    {
        global $_COOKIE;
        $_COOKIE['AfsSessionId'] = 'youhou';
        $mgr = new AfsUserSessionManager('myName');
        $this->assertNull($mgr->get_user_id());
    }
    public function testRetrieveSpecificSessionIdName()
    {
        global $_COOKIE;
        $_COOKIE['AfsSessionId'] = 'youhou';
        $_COOKIE['myName'] = 'wouhahaha';
        $mgr = new AfsUserSessionManager('myName');
        $this->assertEquals('wouhahaha', $mgr->get_user_id());
    }


}

?>
