<?php
require_once "afs_text_helper.php";

class Visitor implements AfsTextVisitorInterface
{
    public $string_text = null;
    public $match_text = null;
    public $trunc_text = null;

    public function visit_AfsStringText(AfsStringText $afs_text)
    {
        $this->string_text = $afs_text->get_text();
        return '<s>' . $this->string_text . '</s>';
    }

    public function visit_AfsMatchText(AfsMatchText $afs_text)
    {
        $this->match_text = $afs_text->get_text();
        return '<m>' . $this->match_text . '</m>';
    }

    public function visit_AfsTruncateText(AfsTruncateText $afs_text)
    {
        $this->trunc_text = $afs_text->get_text();
        return '<t>' . $this->trunc_text . '</t>';
    }
}

class Element
{
    public $text = null;
    public $match = null;

    public function __construct($text, $match)
    {
        $this->text = $text;
        $this->match = $match;
    }
}


class textVisitorTest extends PHPUnit_Framework_TestCase
{
    public function testVisitTextClasses()
    {
        $element = new Element('string', 'match');
        $texts = array(new AfsStringText($element),
            new AfsMatchText($element),
            new AfsTruncateText($element));
        $visitor = new Visitor();
        foreach ($texts as $text) {
            $text->accept($visitor);
        }

        $this->assertTrue($visitor->string_text == 'string');
        $this->assertTrue($visitor->match_text == 'match');
        $this->assertTrue($visitor->trunc_text == '...');
    }

    public function testTextManager()
    {
        $texts = json_decode('[
                                { "afs:t": "KwicString",
                                  "text" : "string" },
                                { "afs:t": "KwicMatch",
                                  "match": "match" },
                                { "afs:t": "KwicString",
                                  "text" : "str" },
                                { "afs:t": "KwicMatch",
                                  "match": "mat" },
                                { "afs:t": "KwicString",
                                  "text" : "s" },
                                { "afs:t": "KwicTruncate" }
                              ]');
        $mgr = new AfsTextManager($texts);
        $visitor = new Visitor();
        $this->assertTrue($mgr->visit_text($visitor)
            == '<s>string</s><m>match</m><s>str</s><m>mat</m><s>s</s><t>...</t>');
    }
}

?>
