<?php

require_once "COMMON/afs_language.php";

class LanguageTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleLanguage()
    {
        $lang = new AfsLanguage('en');
        $this->assertEquals($lang->lang, 'en');
        $this->assertNull($lang->country);
    }

    public function testLanguageWithRegion()
    {
        $lang = new AfsLanguage('en-us');
        $this->assertEquals($lang->lang, 'en');
        $this->assertEquals($lang->country, 'us');
    }

    public function testLanguageWithRegionAndConvenientSeparator()
    {
        $lang = new AfsLanguage('EN_US');
        $this->assertEquals($lang->lang, 'en');
        $this->assertEquals($lang->country, 'us');
    }

    public function testBadLangCode()
    {
        try {
            $lang = new AfsLanguage('enn');
            $this->fail('Should have failed due to invalid language code!');
        } catch (InvalidArgumentException $e) { }
    }

    public function testBadCountryCode()
    {
        try {
            $lang = new AfsLanguage('en-enn');
            $this->fail('Should have failed due to invalid country code!');
        } catch (InvalidArgumentException $e) { }
    }

    public function testBadSeparator()
    {
        try {
            $lang = new AfsLanguage('en~en');
            $this->fail('Should have failed due to invalid language/country separator!');
        } catch (InvalidArgumentException $e) { }
    }

    public function testLanguageAsEmptyString()
    {
        $lang = new AfsLanguage(null);
        $this->assertEquals($lang->get_string(), '');
    }

    public function testLangCodeAsString()
    {
        $lang = new AfsLanguage('fr');
        $this->assertEquals($lang->get_string(), 'fr');
    }

    public function testLangAndRegionCodeAsString()
    {
        $lang = new AfsLanguage('fr_BE');
        $this->assertEquals($lang->get_string(), 'fr-be');
    }
}

?>
