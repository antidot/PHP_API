<?php ob_start();
require_once 'AIF/afs_user_authentication.php';
require_once 'AIF/afs_token_authentication.php';

class AuthenticationTest extends PHPUnit_Framework_TestCase
{
    public function testUserAuthenticationFormat76()
    {
        $auth = new AfsUserAuthentication('foo', 'bar', AFS_AUTH_LDAP);
        $this->assertEquals('login://foo:bar@LDAP', $auth->format_as_url_param('7.6'));
        $this->assertEquals('', $auth->format_as_url_param());
    }
    public function testUserAuthenticationFormatNew()
    {
        $auth = new AfsUserAuthentication('foo', 'bar');
        $this->assertEquals(array('Authorization' => 'Basic ' . base64_encode('foo:bar')), $auth->format_as_header_param());
    }

    public function testTokenAuthenticationFormat76()
    {
        $auth = new AfsTokenAuthentication('foo');
        $this->assertEquals('login://foo@SSO', $auth->format_as_url_param('7.6'));
    }
    public function testTokenAuthenticationFormatNew()
    {
        $auth = new AfsTokenAuthentication('foo');
        $this->assertEquals(array('access-token' => 'foo'), $auth->format_as_header_param());
    }

}
