<?php ob_start();
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

        $this->assertEquals('data 1', $helper->get_clientdata()->get_value('/clientdata/data/data1[2]'));
    }

    public function testGetGeoDataSouldReturnNull () {
        $reply = json_decode('{
                "docId": 180,
                "uri": "http://foo.bar.baz/14",
                "title": [
                    {
                        "afs:t": "KwicString",
                        "text": "The title 14"
                    }
                ]
            }');
        $helper = new AfsReplyHelper($reply);
        $this->assertTrue($helper->get_geo_data() == null);
    }

    public function testGetGeoData() {
        $reply = json_decode('{
                "docId": 180,
                "uri": "http://foo.bar.baz/14",
                "title": [
                    {
                        "afs:t": "KwicString",
                        "text": "The title 14"
                    }
                ],
                "geo_reply_ext": [{

                        "point": {
                            "dist" : 0,
                            "lat"  : 45,
                            "lon"  : 5
                        }
                }]

            }');
        $helper = new AfsReplyHelper($reply);
        $geo_data = $helper->get_geo_data();

        $expected_geo_data = new stdClass();
        $expected_geo_data->dist = 0;
        $expected_geo_data->lat = 45;
        $expected_geo_data->lon = 5;
        $this->assertTrue($geo_data[0]->point == $expected_geo_data);
    }

    public function testHasGeoDataShouldReturnTrue() {
        $reply = json_decode('{
                "docId": 180,
                "uri": "http://foo.bar.baz/14",
                "title": [
                    {
                        "afs:t": "KwicString",
                        "text": "The title 14"
                    }
                ],
                "geo_reply_ext": [{

                        "point": {
                            "dist" : 0,
                            "lat"  : 45,
                            "lon"  : 5
                        }
                }]

            }');

        $helper = new AfsReplyHelper($reply);

        $this->assertTrue($helper->has_geo_data());
    }

    public function testHasGeoDataShouldReturnFalse() {
        $reply = json_decode('{
                "docId": 180,
                "uri": "http://foo.bar.baz/14",
                "title": [
                    {
                        "afs:t": "KwicString",
                        "text": "The title 14"
                    }
                ]
            }');

        $helper = new AfsReplyHelper($reply);

        $this->assertTrue(! $helper->has_geo_data());
    }
}
