<?php
require_once "afs_authentication.php";

class AuthenticationTest extends PHPUnit_Framework_TestCase
{
    public function testGoodParameters()
    {
        $auth = new AfsAuthentication('foo', 'bar', AFS_AUTH_LDAP);
        $this->assertEquals($auth->user, 'foo');
        $this->assertEquals($auth->password, 'bar');
        $this->assertEquals($auth->authority, AFS_AUTH_LDAP);
    }

    public function testBadAuthority()
    {
        try {
            $auth = new AfsAuthentication('foo', 'bar', 'AFS_AUTH_LDAP');
            $this->fail('Should have failed due to invalid authority parameter');
        } catch (InvalidArgumentException $e) { }
    }

}
