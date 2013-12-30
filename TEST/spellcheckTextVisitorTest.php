<?php
require_once "afs_spellcheck_text_visitor.php";

class SpellcheckVisitor implements AfsSpellcheckTextVisitorInterface
{
    public $text = null;
    public $text_pre = null;
    public $match = null;
    public $match_pre = null;

    public function visit_AfsSpellcheckText(AfsSpellcheckText $text)
    {
        $this->text = $text->get_text();
        $this->text_pre = $text->get_pre();
        return new AfsRawAndFormattedText($this->text, '<s>' . $this->text . '</s>');
    }

    public function visit_AfsSpellcheckMatch(AfsSpellcheckMatch $text)
    {
        $this->match = $text->get_text();
        $this->match_pre = $text->get_pre();
        return new AfsRawAndFormattedText($this->match_pre . $this->match,
                     $this->match_pre . '<b>' . $this->match . '</b>');
    }
}


class spellcheckTextVisitorTest extends PHPUnit_Framework_TestCase
{
    public function testVisitTextClasses()
    {
        $element = json_decode('{
                "text": { "text": "foo",
                          "pre": "pret" },
                "match": { "text": "bar",
                           "pre": "prem" }
            }');
        $texts = array(new AfsSpellcheckText($element->text),
            new AfsSpellcheckMatch($element->match));
        $visitor = new SpellcheckVisitor();
        foreach ($texts as $text) {
            $text->accept($visitor);
        }

        $this->assertEquals('pretfoo', $visitor->text);
        $this->assertEquals('', $visitor->text_pre);
        $this->assertEquals('bar' , $visitor->match);
        $this->assertEquals('prem', $visitor->match_pre);
    }

    public function testTextManager()
    {
        $texts = json_decode('[
                    { "text": { "text": "foo",
                                "pre": "pret" } },
                    { "match": { "text": "bar",
                                 "pre": "prem" } },
                    { "text": { "text": "baz" } },
                    { "match": { "text": "bat" } }
                ]');
        $mgr = new AfsSpellcheckTextManager($texts);
        $visitor = new SpellcheckVisitor();
        $text = $mgr->visit_text($visitor);
        $this->assertEquals('<s>pretfoo</s>prem<b>bar</b><s>baz</s><b>bat</b>', $text->formatted);
        $this->assertEquals('pretfooprembarbazbat', $text->raw);
    }
}

?>
