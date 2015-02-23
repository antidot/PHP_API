<?php
require_once 'AFS/afs_configuration_base.php';
require_once 'AFS/SEARCH/afs_query_coder.php';
require_once 'AFS/SEARCH/afs_text_visitor.php';
require_once 'AFS/SEARCH/afs_spellcheck_text_visitor.php';

class TextEncoding extends BasicEnum {
    const UTF8 = 'UTF-8';
    const ISO88591 = 'ISO-8859-1';
}


/** @brief Configuration class for AFS helper classes. */
class AfsHelperConfiguration extends AfsConfigurationBase
{
    private $query_coder = null;
    private $reply_text_visitor = null;
    private $spellcheck_text_visitor = null;
    // encoding used in URLS and response. API support both UTF-8 and ISO-8859-1
    private $text_encoding = TextEncoding::UTF8;

    /** @brief Constructs new configuration class with default parameters set.
     */
    public function __construct()
    {
        parent::__construct();
        $this->reply_text_visitor = new AfsTextVisitor();
        $this->spellcheck_text_visitor = new AfsSpellcheckTextVisitor();
    }

    /**
     * @Brief get the actual user encoding
     * @return the current encoding (UTF-8 or ISO-8859-1)
     */
    public function get_text_encoding() {
        return $this->text_encoding;
    }

    /**
     * @Brief set the user encoding for URL building and response parsing
     *        default used is UTF-8. UTF-8 and ISO-8859-1 are supported
     * @param TextEncoding [in] $encoding
     */
    public function set_text_encoding($encoding) {
        $this->text_encoding = $encoding;
    }

    /** @name Query coder
     * @{ */

    /** @brief Checks whether a query coder has been defined.
     * @return @c True when a query coder is defined, @c false otherwise.
     */
    public function has_query_coder()
    {
        if (is_null($this->query_coder)) {
            return false;
        } else {
            return true;
        }
    }
    /** @brief Retrieves query coder.
     * @return query coder (see AfsQueryCoderInterface).
     */
    public function get_query_coder()
    {
        return $this->query_coder;
    }
    /** @brief Defines new query coder.
     * @param $query_coder [in] new query coder to set.
     * @return current instance.
     */
    public function set_query_coder(AfsQueryCoderInterface $query_coder)
    {
        $this->query_coder = $query_coder;
        return $this;
    }
    /** @} */

    /** @name Reply text
     * @{ */

    /** @brief Retrieves reply text visitor.
     *
     * This visitor is used to format text for title and abstract replies.
     * @return reply text visitor (see AfsTextVisitorInterface).
     */
    public function get_reply_text_visitor()
    {
        return $this->reply_text_visitor;
    }

    /** @brief Defines new reply text visitor.
     * @param $visitor [in] new visitor to set
     * @return current instance.
     */
    public function set_reply_text_visitor(AfsTextVisitorInterface $visitor)
    {
        $this->reply_text_visitor = $visitor;
        return $this;
    }
    /** @} */

    /** @name Spellcheck text
     * @{ */

    /** @brief Retrieves spellcheck text visitor.
     * @return spellcheck text visitor (see AfsSpellcheckTextVisitorInterface).
     */
    public function get_spellcheck_text_visitor()
    {
        return $this->spellcheck_text_visitor;
    }

    /** @brief Defines new spellcheck text visitor.
     * @param $visitor [in] new visitor to set.
     * @return current instance.
     */
    public function set_spellcheck_text_visitor(AfsSpellcheckTextVisitorInterface $visitor)
    {
        $this->spellcheck_text_visitor = $visitor;
        return $this;
    }
    /** @} */
}


