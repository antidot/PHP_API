<?php ob_start();
require_once 'AFS/ACP/afs_acp_replyset_helper.php';
require_once 'AFS/SEARCH/afs_query.php';


class AcpReplysetHelperTest extends PHPUnit_Framework_TestCase
{
    public function testReplyRetrieveSearchQuery()
    {
        $input = json_decode('[
            "search",
            [ "suggest" ]
          ]', true);

        $replyset = new AfsAcpReplysetHelper('', $input);
        $this->assertTrue($replyset->has_reply());
        $this->assertEquals(1, $replyset->get_nb_replies());
        
        $replies = $replyset->get_replies();
        $reply = reset($replies);
        $query = $reply->get_search_query();
        $this->assertEquals('suggest', $query->get_query());
        $this->assertEquals(AfsOrigin::ACP, $query->get_from());
    }

    public function testReplyHasNoOption()
    {
        $input = json_decode('[
            "search",
            [ "suggest" ]
          ]', true);

        $replyset = new AfsAcpReplysetHelper('NoName', $input);
        $this->assertEquals('NoName', $replyset->get_feed());
        $this->assertEquals(1, $replyset->get_nb_replies());

        $replies = $replyset->get_replies();
        $reply = reset($replies);
        $this->assertFalse($reply->has_option());
    }
    public function testReplyHasNamedOption()
    {
        $input = json_decode('[
            "search",
            [ 
              "foo",
              "bar"
            ],
            [
              {
                "k": "v",
                "l": "b"
              },
              { }
            ]
          ]', true);

        $replyset = new AfsAcpReplysetHelper('', $input);
        $this->assertEquals('', $replyset->get_feed());
        $this->assertEquals(2, $replyset->get_nb_replies());

        $replies = $replyset->get_replies();
        $reply = reset($replies);
        $this->assertEquals('foo', $reply->get_value());
        $this->assertTrue($reply->has_option());
        $this->assertTrue($reply->has_option('k'));
        $this->assertFalse($reply->has_option('kk'));
        $this->assertEquals('v', $reply->get_option('k'));
        $this->assertEquals('b', $reply->get_option('l'));

        $reply = next($replies);
        $this->assertFalse($reply->has_option());
    }

    public function testReplyUnknownNamedOption()
    {
        $input = json_decode('[
            "search",
            [ 
              "foo",
              "bar"
            ],
            [
              {
                "k": "v",
                "l": "b"
              },
              { }
            ]
          ]', true);

        $replyset = new AfsAcpReplysetHelper('', $input);
        $replies = $replyset->get_replies();

        $reply = reset($replies);
        $this->assertTrue($reply->has_option());
        try {
            $reply->get_option('blabla');
            $this->fail('Retrieving unknown option should have raised exception!');
        } catch (OutOfBoundsException $e) { }

        $reply = next($replies);
        $this->assertFalse($reply->has_option());
        try {
            $reply->get_option('blabla');
            $this->fail('Retrieving unknown option should have raised exception!');
        } catch (OutOfBoundsException $e) { }
    }

    public function testReplysetHasNoReply()
    {
        $input = json_decode('[
            "search",
            [ ]
          ]', true);

        try {
            $replyset = new AfsAcpReplysetHelper('', $input);
            $this->fail('No suggestion should not allow to create replyset helper!');
        } catch (AfsAcpEmptyReplysetException $e) { }
    }
}
