<?php
/**
 * Created by agaillard
 * Date: 3/26/14
 * Time: 11:12 AM
 */

require_once('AIF/afs_paf_live_connector.php');

class PafLiveTest extends PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $auth = new AfsUserAuthentication('t8', 'antidot', AFS_AUTH_BOWS);
        $service = new AfsService(80108);
        $paf = new AfsPafLiveConnector("eval02.partners.antidot.net", $service, "Live", $auth);
        $response = $paf->process_doc(new AfsDocument(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<root>
<test>Francois Hollande mange des flamby</test>
</root>
XML
, 'text/xml'));
        $contents = $response["CONTENTS"];
        $user = $response["USER_1"];
        $this->assertEquals("text/xml", $contents->get_mime_type());
        $layer_content = $contents->get_content();
        $this->assertFalse(empty($layer_content));
        $xml = simplexml_load_string($layer_content);
        $this->assertEquals("Francois Hollande mange des flamby", $xml->test[0]);
        $layer_content = $user->get_content();
        $this->assertEquals("application/xml", $user->get_mime_type());
        $this->assertFalse(empty($layer_content));
        $xml = simplexml_load_string($layer_content, "SimpleXMLElement", 0, "afs", true);
        $this->assertEquals("Francois Hollande", $xml->entity[0]->attributes()->text);
        //Try to access non existing layer
        try {
            $toto = $response["TOTO"];
        } catch (Exception $e) {
            $this->assertEquals("PHPUnit_Framework_Error_Notice", get_class($e));
        }

    }
}