<?php ob_start();
require_once 'AFS/ACP/afs_acp_response_helper.php';


class AcpResponseHelperTest extends PHPUnit_Framework_TestCase
{
    public function testNoSuggestion()
    {
        $input = json_decode('[
            "foo",
            []
          ]', true);

        $response = new AfsAcpResponseHelper($input);
        $this->assertFalse($response->has_replyset());
    }

    public function testMonoFeed()
    {
        $input = json_decode('[
            "search",
            [ "suggest" ]
          ]', true);

        $response = new AfsAcpResponseHelper($input);
        $this->assertTrue($response->has_replyset());

        $replysets = $response->get_replysets();
        $this->assertEquals(1, count($replysets));

        $replyset = $response->get_replyset();
        $this->assertEquals('', $replyset->get_feed());
    }

    public function testMultiFeed()
    {
        $input = json_decode('{
            "Foo": [
              "search",
              [ "foo" ]
            ],
            "Bar": [
              "search",
              [
                "bar",
                "baz"
              ]
            ]
          }', true);

        $response = new AfsAcpResponseHelper($input);
        $this->assertTrue($response->has_replyset());

        $replysets = $response->get_replysets();
        $this->assertEquals(2, count($replysets));

        $replyset = $response->get_replyset('Foo');
        $this->assertEquals('Foo', $replyset->get_feed());
        $replyset = reset($replysets);
        $this->assertEquals('Foo', $replyset->get_feed());
        $replies = $replyset->get_replies();
        $this->assertEquals(1, count($replies));
        $reply = reset($replies);
        $this->assertEquals('foo', $reply->get_value());

        $replyset = next($replysets);
        $this->assertEquals('Bar', $replyset->get_feed());
        $replies = $replyset->get_replies();
        $this->assertEquals(2, count($replies));
        $reply = reset($replies);
        $this->assertEquals('bar', $reply->get_value());
        $reply = next($replies);
        $this->assertEquals('baz', $reply->get_value());
    }

    public function testUnknownFeed()
    {
        $input = json_decode('{
            "Foo": [
              "search",
              [ "foo" ]
            ],
            "Bar": [
              "search",
              [
                "bar",
                "baz"
              ]
            ]
          }', true);

        $response = new AfsAcpResponseHelper($input);
        $this->assertTrue($response->has_replyset());

        try {
            $response->get_replyset('BAZ');
            $this->fail('Retrieving replyset from unknown feed should have raised exception!');
        } catch (OutOfBoundsException $e) { }
    }

    public function testSingleFeedAsArray()
    {
        $input = json_decode('[
            "search",
            [ "suggest" ],
            [ { "key": "value"} ]
          ]', true);

        $response = new AfsAcpResponseHelper($input);
        $this->assertTrue($response->has_replyset());
        $result = $response->format();

        $this->assertTrue(array_key_exists('', $result));
        $replyset = $result[''];
        $this->assertEquals('', $replyset['feed']);
        $replies = $replyset['replies'];
        $reply = reset($replies);
        $this->assertEquals('suggest', $reply['value']);
        $this->assertFalse(empty($reply['options']));
        $option = each($reply['options']);
        $this->assertEquals('key', $option[0]);
        $this->assertEquals('value', $option[1]);
    }

    public function testMultiFeedsAsArray()
    {
        $input = json_decode('{
            "foo": [
              "search",
              [ "suggest" ],
              [ { "key": "value"} ]
            ],
            "bar": [
              "search",
              [ "sugg" ]
            ]
          }', true);

        $response = new AfsAcpResponseHelper($input);
        $this->assertTrue($response->has_replyset());
        $result = $response->format();

        $this->assertEquals('search', $result['query_string']);

        $this->assertTrue(array_key_exists('foo', $result));
        $replyset = $result['foo'];
        $this->assertEquals('foo', $replyset['feed']);
        $replies = $replyset['replies'];
        $reply = reset($replies);
        $this->assertEquals('suggest', $reply['value']);
        $option = each($reply['options']);
        $this->assertEquals('key', $option[0]);
        $this->assertEquals('value', $option[1]);

        $this->assertTrue(array_key_exists('bar', $result));
        $replyset = $result['bar'];
        $this->assertEquals('bar', $replyset['feed']);
        $replies = $replyset['replies'];
        $reply = reset($replies);
        $this->assertEquals('sugg', $reply['value']);
        $this->assertTrue(empty($reply['options']));
    }
}
