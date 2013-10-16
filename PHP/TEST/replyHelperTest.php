<?php
require_once "afs_reply_helper.php";

class ReplyHelperTest extends PHPUnit_Framework_TestCase
{
    public function testReply()
    {
        $reply = json_decode('{
                "docId": 180,
                "uri": "http://foo.bar.baz/14",
                "title": [
                    {
                        "afs:t": "KwicString",
                        "text": "The title 14"
                    }
                ],
                "abstract": [
                    {
                        "afs:t": "KwicString",
                        "text": "some content "
                    },
                    {
                        "afs:t": "KwicMatch",
                        "match": "match content"
                    },
                    {
                        "afs:t": "KwicString",
                        "text": " other content"
                    },
                    {
                        "afs:t": "KwicTruncate"
                    }
                ],
                "relevance": {
                    "rank": 21
                },
                "clientData": [
                    {
                        "contents": "<clientdata>&lt;data&gt;&lt;data1&gt;data 0&lt;/data1&gt;&lt;data1&gt;data 1&lt;/data1&gt;&lt;multi&gt;&lt;m0&gt;m 0&lt;/m0&gt;&lt;m1&gt;m 1&lt;/m1&gt;&lt;m2&gt;m 2&lt;/m2&gt;&lt;m3&gt;m 3&lt;/m3&gt;&lt;/multi&gt;&lt;/data&gt;</clientdata>",
                        "id": "main",
                        "mimeType": "text/xml"
                    }
                ]
            }');

        $helper = new AfsReplyHelper($reply);
        $this->assertEquals($helper->get_title(), 'The title 14');
        $this->assertEquals($helper->get_abstract(), 'some content <b>match content</b> other content...');
        // Magics
        $this->assertEquals($helper->title, 'The title 14');
        $this->assertEquals($helper->abstract, 'some content <b>match content</b> other content...');
        $this->assertEquals($helper->uri, 'http://foo.bar.baz/14');
    }
}
