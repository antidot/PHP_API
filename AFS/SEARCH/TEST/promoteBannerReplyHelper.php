<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/13/15
 * Time: 4:11 PM
 */
require_once 'AFS/SEARCH/afs_promote_banner_reply_helper.php';


class PromoteBannerHelperTest extends PHPUnit_Framework_TestCase {

    protected function setUp()
    {
        $client_data = '<afs:type xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\">banner</afs:type><afs:images xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\"><afs:image><afs:url>url</afs:url><afs:imageUrl>image_url</afs:imageUrl></afs:image></afs:images>';
        $this->input = json_decode('{
                                "docId": 198,
                                "uri": "http://foo.bar.baz/116",
                                "title": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "The title"
                                    }
                                ],
                                "abstract": [
                                    {
                                        "afs:t": "KwicString",
                                        "text": "viens de tomber a monté d\'un nouveau cran dans l\'étrangeté. Jamais dans l\'Histoire de l\'humanité il n\'a existé de civilisation sans enfants. Je tente d\'en imaginer les conséquences. George, qui m\'a deviné, énumère: - Comme nous ne nous reproduisons pas, la moitié féminine de l\'humanité"
                                    },
                                    { "afs:t": "KwicTruncate" }
                                ],
                                "relevance": {
                                    "rank": 3
                                },
                                "clientData": [
                                    {
                                        "contents": "' . $client_data . '",
                                        "id": "main",
                                        "mimeType": "text/xml"
                                    }]
                                }');

        $this->check_json_error();
    }

    private function check_json_error() {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                break;
            case JSON_ERROR_DEPTH:
                throw new Exception(' - Max depth reached');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception(' - Bad modes or underflow');
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new Exception(' - Bad characters');
                break;
            case JSON_ERROR_SYNTAX:
                throw new Exception(' - Syntax error');
                break;
            case JSON_ERROR_UTF8:
                throw new Exception(' - Bad encoding');
                break;
            default:
                break;
        }
    }

    public function testUrls() {
        $promote_banner_helper = new AfsPromoteBannerReplyHelper($this->input);
        $this->assertEquals($promote_banner_helper->get_url(), 'url');
        $this->assertEquals($promote_banner_helper->get_image_url(), 'image_url');
    }

    /*public function testWithJsonClientData() {
        $input = json_decode('{
                                "docId": 198,
                                "uri": "http://foo.bar.baz/116",
                                "clientData": [
                                    {
                                        "contents": {
                                            "type": "banner",
                                            "images": {
                                                "image": {
                                                        "url": "url",
                                                        "imageUrl" : "image_url"
                                                }
                                            }

                                        },
                                        "id": "main",
                                        "mimeType": "application/json"
                                    }
                                ]}');

        $promote_banner_helper = new AfsPromoteBannerReplyHelper($input);
        $this->assertEquals($promote_banner_helper->get_url(), 'url');
        $this->assertEquals($promote_banner_helper->get_image_url(), 'image_url');
    }*/
}