<?php

/** @brief Base class for AFS spellcheck texts
 *
 * It accepts @a AfsSpellcheckTextVisitorInterface visitor in order to
 * retrieve raw and formatted spellcheck texts.*/
abstract class AfsSpellcheckBaseText
{
    private $pre = null;
    private $text = null;

    /** @brief Constructs instance.
     * @param $text [in] text of the instance.
     * @param $pre [in] prefix text which should never be highlighted
     *        (see @a AfsSpellcheckMatch for more details)
     */
    public function __construct($text, $pre='')
    {
        $this->text = $text;
        $this->pre = $pre;
    }

    /** @brief Retrieves text.
     * @return text.
     */
    public function get_text()
    {
        return $this->text;
    }

    /** @brief Retrieves prefix text.
     * @return prefix text.
     */
    public function get_pre()
    {
        return $this->pre;
    }

    /** @brief Accept visitors.
     * @param $visitor [in] visitor which implements
     *        @a AfsSpellcheckTextVisitorInterface.
     * @return @a AfsRawAndFormattedText returned from visitor call.
     * @exception Exception when necessary method has not been implemented in
     *            @a visitor.
     * @internal Exception should never happen if @a AfsTextVisitorInterface is
     * up to date.
     */
    public function accept(AfsSpellcheckTextVisitorInterface $visitor)
    {
        $visit_methods = get_class_methods($visitor);
        $text_class = get_class($this);

        foreach ($visit_methods as $method) {
            if ('visit_' . $text_class == $method) {
                return $visitor->{'visit_' . $text_class}($this);
            }
        }
        throw new Exception('Visit method not implemented: visit_' . $text_class);
    }
}


/** @brief Implements spellcheck text.
 *
 * When a query is composed of multiple words, instances of this class are
 * created for each valid word (ie words with no suggested correction).*/
class AfsSpellcheckText extends AfsSpellcheckBaseText
{
    /** @brief Constructs spellcheck text instance.
     * @param $text_element [in] a spellcheck text element with or without
     *        prefix text.
     */
    public function __construct($text_element)
    {
        $pre = property_exists($text_element, 'pre')
            ? $text_element->pre
            : '';
        parent::__construct($pre . $text_element->text);
    }
}


/** @brief Implements spellcheck match.
 *
 * When a query is composed of multiple words, instances of this class are
 * created for each invalid word (ie words with suggested correction).*/
class AfsSpellcheckMatch extends AfsSpellcheckBaseText
{
    /** @brief Constructs spellcheck match instance.
     * @param $match_element [in] a spellcheck match element with or without
     *        prefix text.
     */
    public function __construct($match_element)
    {
        $pre = property_exists($match_element, 'pre') ? $match_element->pre : '';
        parent::__construct($match_element->text, $pre);
    }
}


/** @brief Raw and formatted spellcheck text result of spellcheck visitor.
 *
 * Raw text is used to build new query whereas formatted text can be used to
 * display result to final user.
 */
class AfsRawAndFormattedText
{
    /** @brief Raw text. */
    public $raw = null;
    /** @brief formatted text. */
    public $formatted = null;

    /** @brief Constructs new instance with raw and formatted text.
     *
     * If no formatted text is provided, raw text is also used as formatted
     * text.
     *
     * @param $raw [in] raw text.
     * @param $formatted [in] formatted text.
     */
    public function __construct($raw, $formatted=null)
    {
        $this->raw = $raw;
        $this->formatted = is_null($formatted) ? $raw : $formatted;
    }
}


/** @brief Spellcheck text visitor interface to be used along with
 * @a AfsSpellcheckBaseText or nie of its derived class.
 *
 * Implementations of this interface should return @a AfsRawAndFormattedText
 * with valid HTML text set for formatted text.
 */
interface AfsSpellcheckTextVisitorInterface
{
    /** @brief Visit @a AfsSpellcheckText instance.
     * @param $text [in] visited instance.
     * @return Implementations should return @a AfsRawAndFormattedText object.
     */
    public function visit_AfsSpellcheckText(AfsSpellcheckText $text);
    /** @brief Visit @a AfsSpellcheckMatch instance.
     * @param $text [in] visited instance.
     * @return Implementations should return @a AfsRawAndFormattedText object.
     */
    public function visit_AfsSpellcheckMatch(AfsSpellcheckMatch $text);
}


/** @brief Spellcheck manager for simple texts and matched texts. */
class AfsSpellcheckTextManager
{
    private $texts = array();

    /** @brief Constructs spellcheck text manager instance.
     *
     * Each portion of spellcheck text (text, match) is extracted from input
     * parameter and stored with appropriate text type.
     *
     * @param $json_reply [in] corresponds to one spellcheck reply in JSON
     *        format.
     * @exception Exception when @a json_reply parameter is invalid.
     */
    public function __construct(array $json_reply)
    {
        foreach ($json_reply as $element) {
            if (property_exists($element, 'text')) {
                $this->texts[] = new AfsSpellcheckText($element->text);
            } elseif (property_exists($element, 'sep')) {
                $this->texts[] = new AfsSpellcheckText($element->sep);
            } elseif (property_exists($element, 'match')) {
                $this->texts[] = new AfsSpellcheckMatch($element->match);
            } else {
                throw new Exception('Spellcheck should be made of \'text\' or \'match\' only');
            }
        }
    }

    /** @brief Visit all text entries.
     * @param $visitor [in] Visitor used to traverse all text entries.
     * @return concatenated raw and formatted texts stored in
     * @a AfsRawAndFormattedText object.
     */
    public function visit_text(AfsSpellcheckTextVisitorInterface $visitor)
    {
        $raw_result = '';
        $formatted_text = '';
        foreach ($this->texts as $text) {
            $raw_and_formatted = $text->accept($visitor);
            $raw_result .= $raw_and_formatted->raw;
            $formatted_text .= $raw_and_formatted->formatted;
        }
        return new AfsRawAndFormattedText($raw_result, $formatted_text);
    }
}


