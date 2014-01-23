<?php
require_once 'AFS/SEARCH/afs_helper_configuration.php';

// Testing purpose
class TextVisitorMock implements AfsTextVisitorInterface
{
    public function visit_AfsStringText(AfsStringText $afs_text)
    {
        return null;
    }

    public function visit_AfsMatchText(AfsMatchText $afs_text)
    {
        return '<BBB>' . $afs_text->get_text() . '</BBB>';
    }

    public function visit_AfsTruncateText(AfsTruncateText $afs_text)
    {
        return '';
    }
}

class SpellcheckTextVisitorMock implements AfsSpellcheckTextVisitorInterface
{
    public function visit_AfsSpellcheckText(AfsSpellcheckText $spellcheck_text)
    {
        return new AfsRawAndFormattedText('ARG'.$spellcheck_text->get_text().'ARG');
    }

    public function visit_AfsSpellcheckMatch(AfsSpellcheckMatch $spellcheck_text)
    {
        return new AfsRawAndFormattedText($spellcheck_text->get_text());
    }
}


// Tests
class HelperConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveDefaultFormat()
    {
        $config = new AfsHelperConfiguration();
        $this->assertEquals(AfsHelperFormat::ARRAYS, $config->get_helper_format());
        $this->assertTrue($config->is_array_format());
        $this->assertFalse($config->is_helper_format());
    }

    public function testSetAndRetrieveFormat()
    {
        $config = new AfsHelperConfiguration();
        $config->set_helper_format(AfsHelperFormat::HELPERS);
        $this->assertEquals(AfsHelperFormat::HELPERS, $config->get_helper_format());
        $this->assertFalse($config->is_array_format());
        $this->assertTrue($config->is_helper_format());
    }

    public function testSetInvalidFormat()
    {
        $config = new AfsHelperConfiguration();
        try {
            $config->set_helper_format('foo');
            $this->fail('Invalid helper format should have raised exception');
        } catch (InvalidArgumentException $e) { }
    }

    public function testRetrieveFacetManager()
    {
        $config = new AfsHelperConfiguration();
        $mgr = $config->get_facet_manager();
        $this->assertNotNull($mgr);
        $facets = $mgr->get_facets();
        $this->assertTrue(empty($facets));
    }

    public function testSetAndRetrieveFacetManager()
    {
        $config = new AfsHelperConfiguration();
        $mgr = new AfsFacetManager();
        $mgr->add_facet(new AfsFacet('FOO', AfsFacetType::STRING_TYPE));
        $config->set_facet_manager($mgr);

        $mgr = $config->get_facet_manager();
        $this->assertNotNull($mgr);
        $facets = $mgr->get_facets();
        $this->assertFalse(empty($facets));
        $this->assertTrue(array_key_exists('FOO', $facets));
    }

    public function testRetrieveFacetManagerAndUpdate()
    {
        $config = new AfsHelperConfiguration();
        $mgr = $config->get_facet_manager();
        $this->assertNotNull($mgr);
        $facets = $mgr->get_facets();
        $this->assertTrue(empty($facets));
        $mgr->add_facet(new AfsFacet('FOO', AfsFacetType::STRING_TYPE));

        $mgr = $config->get_facet_manager();
        $this->assertNotNull($mgr);
        $facets = $mgr->get_facets();
        $this->assertFalse(empty($facets));
        $this->assertTrue(array_key_exists('FOO', $facets));
    }

    public function testHasQueryCoder()
    {
        $config = new AfsHelperConfiguration();
        $this->assertFalse($config->has_query_coder());
    }

    public function testRetrieveQueryCoder()
    {
        $config = new AfsHelperConfiguration();
        $query_coder = $config->get_query_coder();
        $this->assertTrue(is_null($query_coder));
    }

    public function testSetAndRetrieveQueryCoder()
    {
        $config = new AfsHelperConfiguration();
        $config->set_query_coder(new AfsQueryCoder('foofoo'));
        $query = new AfsQuery();
        $coder = $config->get_query_coder();
        $link = $coder->generate_link($query);
        $this->assertTrue(strstr($link, 'foofoo') !== null);
    }

    public function testRetrieveReplyTextVisitor()
    {
        $config = new AfsHelperConfiguration();
        $visitor = $config->get_reply_text_visitor();
        $this->assertNotNull($visitor);
        $text = new AfsMatchText(json_decode('{"match": "foo"}'));
        $this->assertEquals('<b>foo</b>', $visitor->visit_AfsMatchText($text));
    }

    public function testSetAndRetrieveReplyTextVisitor()
    {
        $config = new AfsHelperConfiguration();
        $visitor = new TextVisitorMock();
        $config->set_reply_text_visitor($visitor);
        $visitor = $config->get_reply_text_visitor();
        $this->assertNotNull($visitor);
        $text = new AfsMatchText(json_decode('{"match": "bar"}'));
        $this->assertEquals('<BBB>bar</BBB>', $visitor->visit_AfsMatchText($text));
    }

    public function testRetrieveSpellcheckTextVisitor()
    {
        $config = new AfsHelperConfiguration();
        $visitor = $config->get_spellcheck_text_visitor();
        $this->assertNotNull($visitor);
        $text = new AfsSpellcheckText(json_decode('{"text": "foo"}'));
        $this->assertEquals('foo', $visitor->visit_AfsSpellcheckText($text)->raw);
    }

    public function testSetAndRetrieveSpellcheckTextVisitor()
    {
        $config = new AfsHelperConfiguration();
        $visitor = new SpellcheckTextVisitorMock();
        $config->set_spellcheck_text_visitor($visitor);
        $visitor = $config->get_spellcheck_text_visitor();
        $this->assertNotNull($visitor);
        $text = new AfsSpellcheckText(json_decode('{"text": "bar"}'));
        $this->assertEquals('ARGbarARG', $visitor->visit_AfsSpellcheckText($text)->raw);
    }

}

?>
