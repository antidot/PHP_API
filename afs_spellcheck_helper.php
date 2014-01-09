<?php

require_once "afs_spellcheck_text_visitor.php";

/** @defgroup default_spellcheck Spellcheck names.
 *
 * Defines names of the spellcheck.
 * @{ */
/** @brief Default spellcheck name when spellcheck is not bind to specific feed. */
define('AFS_DEFAULT_SPELLCHECK', 'afs:spellcheck');
/** @} */


/** @brief Manages one or more spellcheck reply. */
class AfsSpellcheckManager extends AfsHelperBase
{
    private $query = null;
    private $coder = null;
    private $visitor = null;
    private $spellchecks = array();

    /** @brief Constructs new manager instance.
     *
     * @param $query [in] the query which has generated current reply.
     * @param $coder [in] query coder. It is used to generate appropriate links
     *        when spellcheck produces replies. Default value is <tt>null</tt>,
     *        so link can not be generated.
     * @param $visitor [in] visitor used to format spellcheck result. Default
     *        value is <tt>null</tt>, when not specified default visitor is
     *        instanced.
     */
    public function __construct(AfsQuery $query, AfsQueryCoderInterface $coder=null,
        AfsSpellcheckTextVisitorInterface $visitor=null)
    {
        $this->query = $query;
        $this->coder = $coder;
        if (is_null($visitor)) {
            $this->visitor = new AfsSpellcheckTextVisitor();
        } else {
            $this->visitor = $visitor;
        }
    }

    /** @brief Add new spellcheck reply to spellcheck manager.
     * @param $replyset [in] JSON reply corresponding to a spellcheck reply.
     * @exception Exception when invalid replyset has been provided.
     */
    public function add_spellcheck($replyset)
    {
        if ($replyset->meta->producer != AFS_PRODUCER_SPELLCHECK) {
            throw new Exception('Invalid replyset provided for spellcheck initialization');
        }
        foreach ($replyset->content->reply as $reply) {
            $this->add_one_spellcheck($reply);
        }
    }

    /** @brief Retrieves default, available or specified spellcheck.
     * @param $feed [in] Feed for which spellcheck should be retrieved. Default
     *        value is <tt>null</tt>, two cases can occur:
     *        - there is only one spellcheck reply which is returned,
     *        - there is multiple spellcheck replies and one corresponds to
     *          default spellcheck reply (AFS_DEFAULT_SPELLCHECK); this one is
     *          returned.
     * @return spellcheck helper (see @a AfsSpellcheckHelper).
     * @exception OutOfBoundsException when required feed has not produced any
     *            spellcheck reply.
     */
    public function get_spellcheck($feed=null)
    {
        if (is_null($feed)) {
            if (1 == count($this->spellchecks)) {
                foreach ($this->spellchecks as $spellcheck) {
                    return $spellcheck;
                }
            } else {
                $feed = AFS_DEFAULT_SPELLCHECK;
            }
        }
        if (! array_key_exists($feed, $this->spellchecks)) {
            if (! array_key_exists(AFS_DEFAULT_SPELLCHECK, $this->spellchecks)) {
                throw new OutOfBoundsException('No spellcheck available for feed: ' . $feed);
            } else {
                $feed = AFS_DEFAULT_SPELLCHECK;
            }
        }
        return $this->spellchecks[$feed];
    }

    /** @brief Retrieves all spellcheck replies.
     * @return spellcheck replies.
     */
    public function get_spellchecks()
    {
        return $this->spellchecks;
    }

    /** @brief Retrieves spellchecks as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * - key corresponds to the feed name or default spellcheck name
     *   (see AFS_DEFAULT_SPELLCHECK)
     * - value corresponds to spellcheck result (see @a AfsSpellcheckHelper::format).
     */
    public function format()
    {
        $result = array();
        foreach ($this->spellchecks as $feed => $spellcheck) {
            $result[$feed] = array();
            foreach ($spellcheck as $suggest) {
                $result[$feed][] = $suggest->format();
            }
        }
        return $result;
    }

    private function add_one_spellcheck($reply)
    {
        $feed = empty($reply->uri) ? AFS_DEFAULT_SPELLCHECK : $reply->uri;
        $text_mgr = new AfsSpellcheckTextManager($reply->suggestion[0]->items);
        $raw_and_formatted = $text_mgr->visit_text($this->visitor);
        if (! array_key_exists($feed, $this->spellchecks)) {
            $this->spellchecks[$feed] = array();
        }

        $spellcheck_query = $this->query->set_query($raw_and_formatted->raw)
                                ->set_from(AfsOrigin::SPELLCHECK);
        $link = null;
        if (! is_null($this->coder)) {
            $link = $this->coder->generate_link($spellcheck_query);
        }

        $this->spellchecks[$feed][] = new AfsSpellcheckHelper($raw_and_formatted,
            $spellcheck_query, $link);
    }
}


/** @brief Spellcheck helper.
 *
 * Spellcheck helper allows to get access to main spellcheck data:
 * - raw text: it can be used to generate new query or present result without
 *   specific formatting.
 * - formatted text: it is used to present formatted spellcheck result.
 * - query: new query to submit to AFS search engine with spellcheck result as
 *   the new words query.
 * - link: the link to put in HTML result page to allow query of AFS serach
 *   engine. This link is generated only when a query coder has been provided
 *   to spellcheck manager.
 */
class AfsSpellcheckHelper extends AfsHelperBase
{
    private $text = null;
    private $query = null;
    private $link = null;

    /** @brief Constructs new spellcheck helper instance.
     * @param $raw_and_formatted [in] instance of @a AfsRawAndFormattedText.
     * @param $query [in] new query initialized with spellcheck result.
     * @param $link [in] link generated from the @a query or <tt>null</tt> when
     *        no query coder has been provided to spellcheck manager.
     */
    public function __construct(AfsRawAndFormattedText $raw_and_formatted, $query, $link)
    {
        $this->text = $raw_and_formatted;
        $this->query = $query;
        $this->link = $link;
    }

    /** @brief Retrieves raw spellcheck text.
     * @return raw text.
     */
    public function get_raw_text()
    {
        return $this->text->raw;
    }

    /** @brief Retrieves raw formatted text.
     * @return formatted text.
     */
    public function get_formatted_text()
    {
        return $this->text->formatted;
    }

    /** @brief Retrieves the query initialized with spellcheck result.
     *
     * This query can be used to request AFS search engine.
     * @return new query.
     */
    public function get_query()
    {
        return $this->query;
    }

    /** @brief Retrieves the link for the new query.
     *
     * This link can be incorporated into HTML web page in order to query AFS
     * search engine. This link is only available when the spellcheck manager
     * has been initialized with appropriate query coder.
     * @return link.
     */
    public function get_link()
    {
        return $this->link;
    }

    /** @brief Retrieves spellcheck reply as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c raw: raw text of the spellcheck reply,
     * @li @c formatted: formatted text of the spellcheck reply,
     * @li @c link: link generated from the spellcheck reply.
     *
     * @return array filled with key and value pairs.
     */
    public function format()
    {
        return array('raw' => $this->get_raw_text(),
                     'formatted' => $this->get_formatted_text(),
                     'link' => $this->get_link());
    }
}

?>
