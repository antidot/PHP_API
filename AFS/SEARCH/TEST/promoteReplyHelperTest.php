<?php
require_once "AFS/SEARCH/afs_promote_reply_helper.php";

class PromoteReplyHelperTest extends PHPUnit_Framework_TestCase
{
    public function testPromoteWithHighlightedText()
    {
        $reply = json_decode('{
                    "docId": 1,
                    "uri": "http://www.wanimo.com/marques/tresor",
                    "title": [
                        {       
                            "afs:t": "KwicMatch",
                            "match": "Croquettes"
                        },
                        {
                            "afs:t": "KwicString",
                            "text": " Trésor pour chien et chat"
                        }
                    ],
                    "abstract": [
                        {
                            "afs:t": "KwicString",
                            "text": "Sur Wanimo, achetez vos "
                        },
                        {
                            "afs:t": "KwicMatch",
                            "match": "croquettes"
                        },
                        {
                            "afs:t": "KwicString",
                            "text": " a moitie prix"
                        }
                    ],
                    "relevance": {
                        "rank": 1
                    },
                    "clientData": [
                        {
                            "contents": "<afs:customData xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\"><afs:banniere>http://www.wanimo.com/images_media/Image/antidot/banniere_tresor.jpg</afs:banniere></afs:customData>",
                            "id": "main",
                            "mimeType": "text/xml"
                        }
                    ]
                }');
        $helper = new AfsPromoteReplyHelper($reply);
        $this->assertEquals('Croquettes Trésor pour chien et chat', $helper->get_title());
        $this->assertEquals('Sur Wanimo, achetez vos croquettes a moitie prix', $helper->get_abstract());
        $this->assertEquals('http://www.wanimo.com/marques/tresor', $helper->get_uri());
        $this->assertEquals('http://www.wanimo.com/images_media/Image/antidot/banniere_tresor.jpg', $helper->get_custom_data('banniere'));
    }

    public function testRequiredCustomDataNotAvailable()
    {
        $reply = json_decode('{
                    "docId": 1,
                    "uri": "http://www.wanimo.com/marques/tresor",
                    "relevance": {
                        "rank": 1
                    },
                    "clientData": [
                        {
                            "contents": "<afs:customData xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\"><afs:banniere>http://www.wanimo.com/images_media/Image/antidot/banniere_tresor.jpg</afs:banniere></afs:customData>",
                            "id": "main",
                            "mimeType": "text/xml"
                        }
                    ]
                }');
        $helper = new AfsPromoteReplyHelper($reply);
        try {
            $helper->get_custom_data('unknown');
        } catch (Exception $e) {
            return;
        }
        $this->fail('Exception should have been raised for unknown key');
    }

    public function testPromoteRetrieveCustomDataAsArray()
    {
        $reply = json_decode('{
                    "docId": 1,
                    "uri": "http://www.wanimo.com/marques/tresor",
                    "title": [
                        {
                            "afs:t": "KwicString",
                            "text": "Foo"
                        }
                    ],
                    "abstract": [
                        {
                            "afs:t": "KwicString",
                            "text": "Bar"
                        }
                    ],
                    "relevance": {
                        "rank": 1
                    },
                    "clientData": [
                        {
                            "contents": "<afs:customData xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\"><afs:banniere>BAN1</afs:banniere><afs:foo>FOO</afs:foo></afs:customData>",
                            "id": "main",
                            "mimeType": "text/xml"
                        }
                    ]
                }');
        $helper = new AfsPromoteReplyHelper($reply);
        $this->assertEquals(array('banniere' => 'BAN1', 'foo' => 'FOO'),
                            $helper->get_custom_data());
    }
}

?>
