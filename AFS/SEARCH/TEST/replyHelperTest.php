<?php ob_start();
require_once "AFS/SEARCH/afs_reply_helper_factory.php";
require_once "AFS/SEARCH/afs_reply_helper.php";
require_once "AFS/SEARCH/afs_text_helper.php";

class TestTextVisitor implements  AfsTextVisitorInterface {

    /** @brief Visit @a AfsStringText instance.
     * @param $afs_text [in] visited instance.
     */
    public function visit_AfsStringText(AfsStringText $afs_text)
    {
    }

    /** @brief Visit @a AfsMatchText instance.
     * @param $afs_text [in] visited instance.
     */
    public function visit_AfsMatchText(AfsMatchText $afs_text)
    {
    }

    /** @brief Visit @a AfsTruncateText instance.
     * @param $afs_text [in] visited instance.
     */
    public function visit_AfsTruncateText(AfsTruncateText $afs_text)
    {
    }
}

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

    public function testReplyHelperFactoryOnRedirectionPromote() {
        $clientData = '<afs:type xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\">redirection</afs:type>';
        $reply = json_decode('{
                "docId": 180,
                "uri": "http://foo.bar.baz/14",
                "relevance" : {"rank" : 1},
                "clientData": [
                    {
                        "contents": " ' . $clientData . '",
                        "id": "main",
                        "mimeType": "text/xml"
                    }
                ]
            }');

        $text_visitor = new TestTextVisitor();
        $factory = new AfsReplyHelperFactory($text_visitor);

        $promote = $factory->create('Promote', $reply);
        $this->assertTrue($promote instanceof AfsPromoteRedirectReplyHelper);
        $this->assertEquals('http://foo.bar.baz/14', $promote->get_url());
    }

    public function testReplyHelperFactoryOnBannerPromote() {
        $clientData = '<afs:type xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\">banner</afs:type><afs:images xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\"><afs:image><afs:url>http://url</afs:url><afs:imageUrl>http://image/url</afs:imageUrl></afs:image></afs:images>';
        $reply = json_decode('{
                "docId": 180,
                "uri": "http://foo.bar.baz/14",
                "relevance" : {"rank" : 1},
                "clientData": [
                    {
                        "contents": " ' . $clientData . '",
                        "id": "main",
                        "mimeType": "text/xml"
                    }
                ]
            }');

        $text_visitor = new TestTextVisitor();
        $factory = new AfsReplyHelperFactory($text_visitor);

        $promote = $factory->create('Promote', $reply);
        $this->assertTrue($promote instanceof AfsPromoteBannerReplyHelper);
        $this->assertEquals('http://url', $promote->get_url());
        $this->assertEquals('http://image/url', $promote->get_image_url());
    }

    public function testReplyHelperFactoryOnDefaultPromote() {
        $clientData = '<afs:type xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\">default</afs:type><afs:customData xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\"/>';
        $reply = json_decode('{
                "docId": 180,
                "uri": "http://foo.bar.baz/14",
                "relevance" : {"rank" : 1},
                "clientData": [
                    {
                        "contents": " ' . $clientData . '",
                        "id": "main",
                        "mimeType": "text/xml"
                    }
                ]
            }');

        $text_visitor = new TestTextVisitor();
        $factory = new AfsReplyHelperFactory($text_visitor);

        $promote = $factory->create('Promote', $reply);
        $this->assertTrue($promote instanceof AfsPromoteReplyHelper);
    }

    /**
     * @expectedException        AfsUnknowPromoteTypeException
     * @expectedExceptionMessage Unknow promote type: bidon
     */
    public function testUnknowPromoteType () {
        $clientData = '<afs:type xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\">bidon</afs:type><afs:customData xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\"/>';
        $reply = json_decode('{
                "docId": 180,
                "uri": "http://foo.bar.baz/14",
                "relevance" : {"rank" : 1},
                "clientData": [
                    {
                        "contents": " ' . $clientData . '",
                        "id": "main",
                        "mimeType": "text/xml"
                    }
                ]
            }');

        $text_visitor = new TestTextVisitor();
        $factory = new AfsReplyHelperFactory($text_visitor);
        $factory->create('Promote', $reply);
    }
}
