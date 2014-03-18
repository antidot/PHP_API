<?php ob_start();
require_once 'AIF/afs_user_authentication.php';
require_once 'AIF/afs_token_authentication.php';

class AuthenticationTest extends PHPUnit_Framework_TestCase
{
    public function testUserAuthenticationFormat()
    {
        $auth = new AfsUserAuthentication('foo', 'bar');
        $this->assertEquals(base64_encode('foo:bar'), $auth->format());
    }

    public function testTokenAuthenticationFormat()
    {
        $auth = new AfsTokenAuthentication('foo');
        $this->assertEquals('foo', $auth->format());
    }

}
