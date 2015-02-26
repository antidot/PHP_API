<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/13/15
 * Time: 4:56 PM
 */

require_once 'AFS/SEARCH/afs_promote_redirect_reply_helper.php';


class PromoteRedirectReplyHelperTest extends PHPUnit_Framework_TestCase {

    protected function setUp()
    {
        $client_data = '<afs:type xmlns:afs=\"http://ref.antidot.net/7.3/bo.xsd\">redirection</afs:type>';
        $this->input = json_decode('{
                                "docId": 198,
                                "uri": "http://url/for/redirection",
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
        $promote_banner_helper = new AfsPromoteRedirectReplyHelper($this->input);
        $this->assertEquals($promote_banner_helper->get_url(), 'http://url/for/redirection');
    }
}