<?php
require_once "AIF/afs_document_manager.php";

class DocumentManagerTest extends PHPUnit_Framework_TestCase
{
    public function testNoManagedDocument()
    {
        $mgr = new AfsDocumentManager();
        $this->assertFalse($mgr->has_document());
    }

    public function testAddDocument()
    {
        $mgr = new AfsDocumentManager();
        $mgr->add_document(new AfsDocument('foo'));
        $this->assertTrue($mgr->has_document());
        $this->assertEquals(count($mgr->get_documents()), 1);
        $mgr->add_document(new AfsDocument('foo'));
        $this->assertTrue($mgr->has_document());
        $this->assertEquals(count($mgr->get_documents()), 2);
    }

    public function testOverrideDocument()
    {
        $mgr = new AfsDocumentManager();
        $mgr->add_document(new AfsDocument('foo'), 'bar');
        $this->assertTrue($mgr->has_document());
        $this->assertEquals(count($mgr->get_documents()), 1);
        $mgr->add_document(new AfsDocument('baz'), 'bar');
        $this->assertTrue($mgr->has_document());
        $this->assertEquals(count($mgr->get_documents()), 1);
        $filename = reset($mgr->get_documents())->get_filename();
        $this->assertEquals(file_get_contents($filename), 'baz');
    }

    public function testAddInvalidDocument()
    {
        $mgr = new AfsDocumentManager();
        try {
            $mgr->add_document(new AfsDocument());
            $this->fail('Should have failed due to invalid document provided');
        } catch (InvalidArgumentException $e) { }
    }
}

?>
