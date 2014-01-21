<?php
require_once "AFS/SEARCH/afs_spellcheck_text_helper.php";

/** @brief Default implementation for spellcheck text visitor. */
class AfsSpellcheckTextVisitor implements AfsSpellcheckTextVisitorInterface
{
    /** @brief Visit @a AfsSpellcheckText instance.
     * @param $spellcheck_text [in] visited instance.
     * @return @a AfsRawAndFormattedText object initialized with
     * @a AfsSpellcheckText instance.
     */
    public function visit_AfsSpellcheckText(AfsSpellcheckText $spellcheck_text)
    {
        return new AfsRawAndFormattedText($spellcheck_text->get_text());
    }

    /** @brief Visit @a AfsSpellcheckMatch instance.
     * @param $spellcheck_text [in] visited instance.
     * @return @a AfsRawAndFormattedText object initialized with
     * @a AfsSpellcheckMatch instance.
     */
    public function visit_AfsSpellcheckMatch(AfsSpellcheckMatch $spellcheck_text)
    {
        $pre = $spellcheck_text->get_pre();
        $text = $spellcheck_text->get_text();
        return new AfsRawAndFormattedText($pre . $text, $pre . '<b>' . $text . '</b>');
    }
}

?>
