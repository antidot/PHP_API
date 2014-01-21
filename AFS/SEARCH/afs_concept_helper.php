<?php
require_once "COMMON/afs_helper_base.php";
require_once "AFS/SEARCH/afs_producer.php";

/** @defgroup default_concept Concept names.
 *
 * Defines names of the concept.
 * @{ */
/** @brief Default concept name when concept is not bind to specific feed. */
define('AFS_DEFAULT_CONCEPT', 'concept');
/** @} */


/** @brief Manager for concept helpers.
 *
 * Each concept result is retrieved as a concept concept helper (see
 * AfsConceptItem).
 */
class AfsConceptManager extends AfsHelperBase
{
    private $concepts = array();


    /** @brief Adds new concept reply to concept manager.
     * @param $replyset [in] JSON reply corresponding to a concept reply.
     * @exception Exception when invalid replyset has been provided.
     */
    public function add_concept($replyset)
    {
        if (AfsProducer::CONCEPT != $replyset->meta->producer) {
            throw new Exception('Invalid replyset provided for concept initialization');
        }
        $feed = $replyset->meta->uri;
        foreach ($replyset->content->reply as $reply) {
            if (property_exists($reply->concept->concepts,'concept')) {
                $this->add_one_concept($reply, $feed);
            }
        }
    }

    /** @brief Checks whether at least one concept is available.
     * @return @c True when one or more concepts is available, @c false 
     * otherwise.
     */
    public function has_concept()
    {
        return count($this->concepts) > 0;
    }

    /** @brief Retrieves all concept helpers.
     * @return concept replies.
     */
    public function get_concepts()
    {
        return $this->concepts;
    }

    /** @brief Retrieves default, available or specified concept.
     *
     * @param $feed [in] Feed for which concept should be retrieved. Default value
     *        is @c null; two cases can occur:
     *        - there is only one concept reply, this reply is returned,
     *        - there is multiple concept replies and one corresponds to default
     *          concept reply (AFS_DEFAULT_CONCEPT), this one is returned.
     *
     * @return appropriate concept helper (see @a AfsConceptHelper).
     * @exception OutOfBoundsException when required feed has not produced any
     *            concept reply.
     */
    public function get_concept($feed=null)
    {
        if (is_null($feed)) {
            if (1 == count($this->concepts)) {
                return reset($this->concepts);
            } else {
                $feed = AFS_DEFAULT_CONCEPT;
            }
        }
        if (! array_key_exists($feed, $this->concepts)) {
            throw new OutOfBoundsException('No spellcheck available for feed: ' . $feed);
        }
        return $this->concepts[$feed];
    }

    private function add_one_concept($reply, $feed)
    {
        $buffer = array();
        if (! array_key_exists($feed, $this->concepts)) {
            $this->concepts[$feed] = new AfsConceptHelper($feed);
        }

        foreach ($reply->concept->concepts->concept as $concept) {
            $buffer[$concept->uri] = $concept->contents;
        }
        foreach ($reply->concept->query->items as $item) {
            if (property_exists($item, 'afs:t')) {
                $this->concepts[$feed]->add_item($item, $buffer);
            } else {
                throw new Exception('No type specified for concept value!');
            }
        }
    }
}


/** @brief Simple access to one concepts of one agent.
 */
class AfsConceptHelper extends AfsHelperBase
{
    private $feed = null;
    private $items = array();

    /** @brief Constructs new concept instance.
     * @param $feed [in] name of the feed this concept is binded to.
     */
    public function __construct($feed)
    {
        $this->feed = $feed;
    }

    /** @brief Adds new concept item to current helper.
     *
     * @param $item [in] values to initialize new concept item with.
     * @param $buffer [in] buffer of concept data.
     */
    public function add_item($item, $buffer)
    {
        $this->items[] = new AfsConceptItem($item, $buffer);
    }

    /** @brief Retrieves all concept items of current concept.
     * @return concept items (see @a AfsConceptItem).
     */
    public function get_items()
    {
        return $this->items;
    }

    /** @brief Retrieves feed name which has produced this concept.
     * @return feed name.
     */
    public function get_feed()
    {
        return $this->feed;
    }
}


/** @brief Concept item.
 *
 * Concept results are made of one or more concept items. Concept items can have
 * no data, one data or even multiple data attached to them.
 */
class AfsConceptItem
{
    private $text = null;
    private $data = array();

    /** @brief Constructs new concept item.
     *
     * @param $item [in] new values to initialize the instance with.
     * @param $concepts [in] buffer of concept data (read only).
     *
     * @exception Exception when invalid/unrecognized data has been provided.
     */
    public function __construct($item, $concepts)
    {
        $this->text = $item->text;
        if ('QueryMatch' == $item->{'afs:t'}) {
            foreach ($item->uri as $uri) {
                $this->data[$uri] = $concepts[$uri];
            }
        } elseif ('QueryText' != $item->{'afs:t'}) {
            throw new Exception('Unmanaged concept type: ' . $item->{'afs:t'});
        }
    }

    /** @brief Retrieves text of the item.
     * @return text of the item.
     */
    public function get_text()
    {
        return $this->text;
    }

    /** @brief Check whether current item has a concept.
     *
     * For multi-words queries, no, one or more words can match one or more
     * concepts. So, this method allow to check whether current item has binded
     * concept(s) or not.
     *
     * @return @c True when current item has at least one binded concept,
     * @c false otherwise.
     */
    public function has_concept()
    {
        return ! empty($this->data);
    }

    /** @brief Retrieves concept data associated to current item.
     *
     * Data are store as key/value pairs. Keys correspond to the URI of the
     * concept. Values correspond to XML data of the concept.
     *
     * @remark to avoid getting empty data, check whether concept is available
     * for current item by calling @a has_concept.
     *
     * @return key/value map of URI/concept or @c null when no concept matches
     * the item text.
     */
    public function get_data()
    {
        return $this->data;
    }
}

?>
