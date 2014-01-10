<?php
require_once "afs_header_helper.php";

class HeaderHelperTest extends PHPUnit_Framework_TestCase
{
    public function testError()
    {
        $input = json_decode('{
                "header": {
                    "query": {
                        "userId": "?",
                        "sessionId": "?",
                        "date": "2014-01-10T16:04:32+0000",
                        "queryParam": [ ],
                        "mainCtx": {
                            "textQuery": ""
                        },
                        "textQuery": ""
                    },
                    "user": {
                        "requestMethod": "GET",
                        "agent": "Mozilla/5.0 (X11; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0 Iceweasel/24.0",
                        "address": "10.61.8.236",
                        "output": {
                            "format": "JSON",
                            "encoding": "gzip",
                            "charset": "UTF-8"
                        }
                    },
                    "info": { },
                    "error": {
                        "message": [
                            "errorMsg"
                        ]
                    }
                }
            }');
        $header = new AfsHeaderHelper($input->header);
        $this->assertTrue($header->in_error());
        $this->assertEquals('errorMsg', $header->get_error());
    }

    public function testNotInError()
    {
        $input = json_decode('{
                "header": {
                    "query": {
                        "userId": "e3eddaff-5a3d-4807-8fb2-09e13baf78e1",
                        "sessionId": "4c0f28d1-bb67-469b-86bf-54f83432914e",
                        "date": "2014-01-10T16:17:44+0000",
                        "queryParam": [ ],
                        "mainCtx": {
                            "textQuery": ""
                        },
                        "textQuery": ""
                    },
                    "user": {
                        "requestMethod": "GET",
                        "agent": "Mozilla/5.0 (X11; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0 Iceweasel/24.0",
                        "address": "10.61.8.236",
                        "output": {
                            "format": "JSON",
                            "encoding": "gzip",
                            "charset": "UTF-8"
                        }
                    },
                    "performance": {
                        "durationMs": 204
                    },
                    "info": { }
                }
            }');
        $header = new AfsHeaderHelper($input->header);
        $this->assertFalse($header->in_error());
        $this->assertEquals('e3eddaff-5a3d-4807-8fb2-09e13baf78e1', $header->get_user_id());
        $this->assertEquals('4c0f28d1-bb67-469b-86bf-54f83432914e', $header->get_session_id());
        $this->assertEquals(204, $header->get_duration());
    }
}

?>
