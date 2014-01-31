<?php
require_once 'AFS/SEARCH/afs_header_helper.php';
require_once 'AFS/SEARCH/afs_replyset_helper.php';
require_once 'AFS/SEARCH/afs_promote_replyset_helper.php';
require_once 'AFS/SEARCH/afs_spellcheck_helper.php';
require_once 'AFS/SEARCH/afs_concept_helper.php';
require_once 'AFS/SEARCH/afs_producer.php';
require_once 'AFS/SEARCH/afs_helper_configuration.php';
require_once 'COMMON/afs_helper_base.php';
require_once 'COMMON/afs_helper_format.php';

/** @brief Main helper for AFS search reply.
 *
 * This helper is intended to be initiliazed with the reply provided by @a 
 * AfsSearchQueryManager::send. It allows to manage replies of one of the
 * available replysets, including facets and pager. Connection and query errors
 * are managed in a uniform way to simplify integration.
 */
class AfsResponseHelper extends AfsHelperBase
{
    private $config = null;
    private $header = null;
    private $replysets = array();
    private $spellcheck_mgr = null;
    private $promote = null;
    private $concepts = null;
    private $error = null;

    /** @brief Construct new response helper instance.
     *
     * @param $response [in] result from @a AfsSearchQueryManager::send call.
     * @param $query [in] query which has produced current reply.
     * @param $config [in] helper configuration object.
     *
     * @exception InvalidArgumentException when one of the parameters is 
     * invalid.
     */
    public function __construct($response, AfsQuery $query,
        AfsHelperConfiguration $config)
    {
        $this->config = $config;
        $this->header = new AfsHeaderHelper($response->header);

        if (property_exists($response, 'replySet')) {
            $query->update_user_and_session_id($this->header->get_user_id(),
                $this->header->get_session_id());

            $this->spellcheck_mgr = new AfsSpellcheckManager($query, $config);
            $this->concepts = new AfsConceptManager();

            $this->initialize_replysets($response->replySet, $query, $config);
        } elseif ($this->header->in_error()) {
            $this->error = $this->header->get_error();
        } else {
            $this->error = 'Unmanaged error';
        }
    }

    private function initialize_replysets($replysets, AfsQuery $query,
        AfsHelperConfiguration $config)
    {
        foreach ($replysets as $replyset) {
            if (property_exists($replyset, 'meta')
                    && property_exists($replyset->meta, 'producer')) {
                $producer = $replyset->meta->producer;
                if ($producer == AfsProducer::SEARCH) {
                    if ('Promote' == $replyset->meta->uri) {
                        $this->promote = new AfsPromoteReplysetHelper($replyset, $config);
                    } else {
                        $replyset_helper = new AfsReplysetHelper($replyset, $query, $config);
                        $feed = $replyset_helper->get_meta()->get_feed();
                        if ($config->is_array_format()) {
                            $this->replysets[$feed] = $replyset_helper->format();
                        } else {
                            $this->replysets[$feed] = $replyset_helper;
                        }
                    }
                } elseif ($producer == AfsProducer::SPELLCHECK) {
                    $this->spellcheck_mgr->add_spellcheck($replyset);
                } elseif ($producer == AfsProducer::CONCEPT) {
                    $this->concepts->add_concept($replyset);
                }
            }
        }
        if ($config->is_array_format()) {
            if (! is_null($this->promote)) {
                $this->promote = $this->promote->format();
            }
        }
    }

    /** @name Replies
     * @{ */

    /** @brief Check whether reponse has a reply.
     * @return true when a reply is available, false otherwise.
     */
    public function has_replyset()
    {
        return (! $this->in_error()) && (! empty($this->replysets));
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
                return reset($this->replysets);
            }
        } elseif (array_key_exists($feed, $this->replysets)) {
            return $this->replysets[$feed];
        }
        throw new OutOfBoundsException('No reply set '
            . (is_null($feed) ? '' : 'named \'' . $feed . '\' '). 'available');
    }
    /** @} */

    /** @name Spellcheck
     * @{ */

    /** @brief Checks whether at least one spellcheck is defined.
     * @return @c True when one or more spellchecks are defined, @c false
     * otherwise.
     */
    public function has_spellcheck()
    {
        return (! $this->in_error()) and $this->spellcheck_mgr->has_spellcheck();
    }
    /** @brief Retrieves spellchecks from the @a response.
     * @return list of @a AfsSpellcheckHelper or formatted spellcheck depending
     * on parameter initialization.
     */
    public function get_spellchecks()
    {
        return $this->spellcheck_mgr->get_spellchecks();
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
        return $this->spellcheck_mgr->get_spellcheck($feed);
    }
    /** @} */

    /** @name Concept
     * @{ */

    /** @brief Checks whether at least one concept is available.
     * @return @c True when one or more concepts is available, @c false otherwise.
     */
    public function has_concept()
    {
      return (! $this->in_error()) and $this->concepts->has_concept();
    }
    /** @brief Retrieves all concept helpers.
     * @return concept replies.
     */
    public function get_concepts()
    {
      return$this->concepts->get_concepts();
    }
    /** @brief Retrieves default or specified concept.
     *
     * For more details see @a AfsConceptManager::get_concept.
     *
     * @param $feed [in] Concept generated by this feed should be retrieved
     *        (default: default concept is retrieved).
     *
     * @return appropriate concept.
     */
    public function get_concept($feed=null)
    {
      return $this->concepts->get_concept($feed);
    }
    /** @} */

    /** @name Miscellaneaous
     * @{ */

    /** @brief Retrieves AFS search engine computation duration.
     *
     * Individual durations are available for each replyset, see
     * @a AfsReplysetHelper for more details.
     *
     * @return Computation duration in milliseconds.
     */
    public function get_duration()
    {
        return $this->header->get_duration();
    }

    /** @brief Retrieve reply data as array.
     *
     * This method is intended for internal use only.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c replysets: replies per feed,
     * @li @c spellchecks: spellcheck replies per feed.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        if ($this->in_error()) {
            return array('error' => $this->get_error_msg());
        } else {
            $result = array('duration' => $this->get_duration());
            if ($this->has_replyset())
                $result['replysets'] = $this->get_replysets();
            if ($this->spellcheck_mgr->has_spellcheck())
                $result['spellchecks'] = $this->spellcheck_mgr->format();
            return $result;
        }
    }
    /** @} */

    /** @name Error
     * @{ */

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
    /** @} */
}


