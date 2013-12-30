<?php
require_once "afs_replyset_helper.php";
require_once "afs_spellcheck_helper.php";
require_once "afs_helper_base.php";

/** @defgroup helper_format Helper format
 *
 * Specify in which format helpers are generated.
 * @{ */
/** @brief Outputs from response helper and sub-sequent child helpers are 
 * instances of helper classes. */
define('AFS_HELPER_FORMAT', 1);
/** @brief Outputs from response helper and sub-sequent child helpers are 
 * array of key/value pairs.
 *
 * This is the prefered format to use in combination with PHP template engines. 
 */
define('AFS_ARRAY_FORMAT', 2);
/** @} */

/** @brief Main helper for AFS search reply.
 *
 * This helper is intended to be initiliazed with the reply provided by @a 
 * AfsSearchQueryManager::send. It allows to manage replies of one of the
 * available replysets, including facets and pager. Connection and query errors
 * are managed in a uniform way to simplify integration.
 */
class AfsResponseHelper extends AfsHelperBase
{
    private $replysets = array();
    private $spellchecks = null;
    private $error = null;

    /** @brief Construct new response helper instance.
     *
     * @param $response [in] result from @a AfsSearchQueryManager::send call.
     * @param $facet_mgr [in] @a AfsFacetManager used to create appropriate
     *        queries.
     * @param $query [in] query which has produced current reply.
     * @param $coder [in] @a AfsQueryCoderInterface if set it will be used to
     *        create links (default: null).
     * @param $format [in] if set to AFS_ARRAY_FORMAT (default), all underlying
     *        helpers will be formatted as array of data, otherwise they are
     *        kept as is. See @ref helper_format for more details.
     * @param $visitor [in] text visitor implementing @a AfsTextVisitorInterface
     *        used to extract title and abstract contents. If not set, default
     *        visitor is used (see @a AfsReplyHelper).
     *
     * @exception InvalidArgumentException when one of the parameters is 
     * invalid.
     */
    public function __construct($response, AfsFacetManager $facet_mgr,
        AfsQuery $query, AfsQueryCoderInterface $coder=null,
        $format=AFS_ARRAY_FORMAT, AfsTextVisitorInterface $visitor=null,
        AfsSpellcheckTextVisitorInterface $spellcheck_visitor=null)
    {
        $this->check_format($format);
        $this->spellchecks = new AfsSpellcheckManager($query, $coder,
            $spellcheck_visitor);

        if (property_exists($response, 'replySet')) {
            $this->initialize_replysets($response->replySet, $facet_mgr, $query,
                $coder, $format, $visitor);
        } elseif (property_exists($response->header, 'error')) {
            $this->error = $response->header->error->message[0];
        } else {
            $this->error = 'Unmanaged error';
        }
    }

    private function initialize_replysets($replysets,
        AfsFacetManager $facet_mgr, AfsQuery $query,
        AfsQueryCoderInterface $coder=null, $format=AFS_ARRAY_FORMAT,
        AfsTextVisitorInterface $visitor=null)
    {
        foreach ($replysets as $replyset) {
            if (property_exists($replyset, 'meta')
                    && property_exists($replyset->meta, 'producer')) {
                $producer = $replyset->meta->producer;
                if (AFS_PRODUCER_SEARCH == $producer) {
                    $replyset_helper = new AfsReplysetHelper($replyset,
                        $facet_mgr, $query, $coder, $format, $visitor);
                    $this->replysets[] = $format == AFS_ARRAY_FORMAT
                        ? $replyset_helper->format()
                        : $replyset_helper;
                } elseif (AFS_PRODUCER_SPELLCHECK == $producer) {
                    $this->spellchecks->add_spellcheck($replyset);
                }
            }
        }
        if (AFS_ARRAY_FORMAT == $format) {
            $this->spellchecks = $this->spellchecks->format();
        }
    }

    /** @brief Check whether reponse has a reply.
     * @return true when a reply is available, false otherwise.
     */
    public function has_replyset()
    {
        return ! empty($this->replysets);
    }

    /** @brief Retrieves all replysets.
     * @return all defined reply sets.
     */
    public function get_replysets()
    {
        return $this->replysets;
    }

    /** @brief Retrieves replyset from the @a response.
     *
     * @param $feed [in] name of the feed to filter on
     *        (default: null -> retrieves first replyset).
     * @return @a AfsReplysetHelper or formatted replyset depending on @a format
     * parameter.
     */
    public function get_replyset($feed=null)
    {
        if (is_null($feed)) {
            if ($this->has_replyset()) {
                return $this->replysets[0];
            }
        } else {
            foreach ($this->replysets as $replyset) {
                $meta = $replyset->get_meta();
                if ($meta->get_feed() == $feed
                    && $meta->get_producer() == AFS_PRODUCER_SEARCH) {
                        return $replyset;
                }
            }
        }
        throw new OutOfBoundsException('No reply set '
            . (is_null($feed) ? '' : 'named \'' . $feed . '\' '). 'available');
    }

    /** @brief Retrieves spellchecks from the @a response.
     * @return spellcheck manager or formatted spellcheck depending on
     * parameter initialization.
     */
    public function get_spellchecks()
    {
        return $this->spellchecks;
    }

    /** @brief Retrieve reply data as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c replysets: replies per feed,
     * @li @c spellchecks: spellcheck replies per feed.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        return array('replysets' => $this->get_replysets(),
                     'spellchecks' => $this->get_spellchecks());
    }

    /** @brief Check whether an error has been raised.
     * @return True on error, false otherwise.
     */
    public function in_error()
    {
        return ! is_null($this->error);
    }

    /** @brief Retrieve error message.
     * @return Error message.
     */
    public function get_error_msg()
    {
        return $this->error;
    }
}

?>
