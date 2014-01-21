<?php
require_once "AFS/SEARCH/afs_reply_helper.php";

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
                        "contents": "<clientdata><data><data1>data 0</data1><data1>data 1</data1><multi><m0>m 0</m0><m1>m 1</m1><m2>m 2</m2><m3>m 3</m3></multi></data></clientdata>",
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

        $this->assertEquals('data 1', $helper->get_clientdata()->get_text('/clientdata/data/data1[2]'));
    }
}
