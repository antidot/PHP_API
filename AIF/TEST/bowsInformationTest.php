<?php ob_start();
require_once 'AIF/afs_bows_information.php';


class BowsInformationTest extends PHPUnit_Framework_TestCase
{
    public function testVersionNumbers()
    {
        $input = json_decode('
            {

                "x:type": "ws.response",
                "query": {
                    "x:type": "ws.response.query",
                    "parameters": {
                        "x:type": "collection",
                        "x:values": [ ]
                    },
                    "properties": { "x:type": "x:dynamic" }
                },
                "result": {
                    "x:type": "bows.about",
                    "boWsVersion": {
                        "x:type": "AfsVersion",
                        "build": "4431cc2043a60285fda2c37d684ebd969cb62a7e",
                        "gen": "7.6",
                        "major": "4",
                        "minor": "1",
                        "motto": "Pink Dolphin"
                    },
                    "copyright": "Copyright (C) 1999-2014 Antidot"
                }

            }');

        $infos = new AfsBOWSInformation($input);
        $this->assertEquals('7.6', $infos->get_gen_version());
        $this->assertEquals('4', $infos->get_major_version());
        $this->assertEquals('1', $infos->get_minor_version());
    }

    public function testInvalidInput()
    {
        try {
            new AfsBOWSInformation('foo');
            $this->fail('Invalid input should have raised exception!');
        } catch (AfsBOWSInvalidReplyException $e) { }
    }
}
