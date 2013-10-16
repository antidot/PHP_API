<?php
require_once "afs_document.php";

class DocumentTest extends PHPUnit_Framework_TestCase
{
    public function testXmlDocumentMimeType()
    {
        $doc = new AfsDocument('<?xml version="1.0" ?><root><foo/></root>');
        $this->assertEquals($doc->get_mime_type(), 'application/xml');
    }

    public function testJsonDocumentMimeType()
    {
        $doc = new AfsDocument('{ "root" : { "value" : "foo", "complex" : [ 1, "bar", 42 ] } }');
        //$this->assertEquals($doc->get_mime_type(), 'application/json');
        $this->assertEquals($doc->get_mime_type(), 'text/plain');
    }

    public function testDocumentValidity()
    {
        $doc = new AfsDocument();
        $this->assertFalse($doc->is_valid());
        $doc->set_content('foo');
        $this->assertTrue($doc->is_valid());
    }

    public function testExistingDocument()
    {
        $doc = new AfsDocument();
        $doc->set_content_from_file(__FILE__);
        $this->assertTrue($doc->is_valid());
        $this->assertEquals($doc->get_filename(), __FILE__);
    }

    public function testDocumentFromContent()
    {
        $content = 'foo is THE content';
        $doc = new AfsDocument($content);
        $filename = $doc->get_filename();
        $this->assertTrue(file_exists($filename));
        $read_content = file_get_contents($filename);
        $this->assertEquals($read_content, $content);
    }

    public function testOnlyOneTemporaryDocumentIsCreated()
    {
        $content = 'foo is THE content';
        $doc = new AfsDocument($content);
        $filename1 = $doc->get_filename();
        $filename2 = $doc->get_filename();
        $this->assertEquals($filename1, $filename2);
    }
}

?>
