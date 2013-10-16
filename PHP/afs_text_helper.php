<?php

/** @brief Base class for AFS texts which accept @a AfsTextVisitorInterface
 * visitors. */
abstract class AfsText
{
    private $text = null;

    /** @brief Construct instance with appropriate text.
     * @param $text [in] text associated to the instance.
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /** @brief Retrieve text.
     * @return text.
     */
    public function get_text()
    {
        return $this->text;
    }

    /** @brief Accept visitors.
     * @param $visitor [in] visitor which implements @a AfsTextVisitorInterface.
     * @return value returned from visitor call.
     * @exception Exception when necessary method has not been implemented in
     *            @a visitor.
     * @internal Exception should never happen if @a AfsTextVisitorInterface is
     * up to date.
     */
    public function accept(AfsTextVisitorInterface $visitor)
    {
        $visit_methods = get_class_methods($visitor);
        $text_class = get_class($this);

        foreach ($visit_methods as $method)
        {
            if ('visit_' . $text_class == $method)
            {
                return $visitor->{'visit_' . $text_class}($this);
            }
        }
        throw new Exception('Visit method not implemented: visit_' . $text_class);
    }
}


/** @brief Implements string text. */
class AfsStringText extends AfsText
{
    /** @brief Construct string text instance.
     * @param $element [in] a title or abstract element. This element is composed
     *        of AFS type (<tt>afs:t</tt>) '<tt>KwicString</tt>' and the
     *        associated <tt>text</tt>.
     */
    public function __construct($element)
    {
        parent::__construct($element->text);
    }
}

/** @brief Implements match text. */
class AfsMatchText extends AfsText
{
    /** @brief Construct match text instance.
     * @param $element [in] a title or abstract element. This element is composed
     *        of AFS type (<tt>afs:t</tt>) '<tt>KwicMatch</tt>' and the
     *        associated <tt>match</tt> text.
     */
    public function __construct($element)
    {
        parent::__construct($element->match);
    }
}

/** @brief Implements truncate text. */
class AfsTruncateText extends AfsText
{
    /** @brief Construct truncate text instance.
     * @param $element [in] a title or abstract element. This element is composed
     *        of AFS type (<tt>afs:t</tt>) '<tt>KwicTruncate</tt>'.
     */
    public function __construct($element)
    {
        parent::__construct('...');
    }
}


/** @brief Text visitor interface to be used along with @a AfsText or one of its
 * derived class.
 *
 * Implementations of this interface should return valid HTML content for each
 * visited @a AfsText instance.<br/>
 * Example of implementation is provided by @a AfsTextVisitor.
 */
interface AfsTextVisitorInterface
{
    /** @brief Visit @a AfsStringText instance.
     * @param $afs_text [in] visited instance.
     */
    public function visit_AfsStringText(AfsStringText $afs_text);

    /** @brief Visit @a AfsMatchText instance.
     * @param $afs_text [in] visited instance.
     */
    public function visit_AfsMatchText(AfsMatchText $afs_text);

    /** @brief Visit @a AfsTruncateText instance.
     * @param $afs_text [in] visited instance.
     */
    public function visit_AfsTruncateText(AfsTruncateText $afs_text);
}


/** @brief Title and content text manager.
 *
 * Provided a JSON result, this manager allows simple way to present result
 * title or result content.
 */
class AfsTextManager
{
    private $texts = array();

    /** @brief Construct text manager instance.
     *
     * Each portion of text (text, match and truncate) is extracted from input
     * parameter and store with appropriate text type.<br/>
     * Two methods are available to traverse these portions of text:
     * - @a visit_text method using  predefined visitor (@a AfsTextVisitor) or
     *   user defined visitor. This is the prefered method.
     * - @a get_texts method which returns an array of @a AfsText (or one of its
     *   derived class) instances.
     *
     * @param $json_reply [in] corresponds to one title or abstract reply in
     *        JSON format.
     * @exception Exception when provided @a json_reply parameter is invalid.
     */
    public function __construct(array $json_reply)
    {
        foreach ($json_reply as $element)
        {
            if (! array_key_exists('afs:t', $element))
            {
                throw new Exception('Input array must contains elements composed of afs type entry (afs:t)');
            }

            $type = $element->{'afs:t'};
            if ($type == 'KwicString') {
                $this->texts[] = new AfsStringText($element);
            } elseif ($type == 'KwicMatch') {
                $this->texts[] = new AfsMatchText($element);
            } elseif ($type == 'KwicTruncate') {
                $this->texts[] = new AfsTruncateText($element);
            } else {
                throw new Exception('Unmanaged afs type: ' . $type);
            }
        }
    }

    /** @brief Visit all text entries.
     * @param $visitor [in] Visitor used to traverse all text entries.
     * @return concatenated text from visited text entries.
     */
    public function visit_text(AfsTextVisitorInterface $visitor)
    {
        $result = '';
        foreach ($this->texts as $text) {
            $result .= $text->accept($visitor);
        }
        return $result;
    }

    /** @brief Retrieves all text entries.
     * @return array of @a AfsText or one of its derived class.
     */
    public function get_texts()
    {
        return $this->texts;
    }
}

?>
