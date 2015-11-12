<?php
require_once 'AFS/afs_response_helper_base.php';
require_once 'AFS/SEARCH/afs_header_helper.php';
require_once 'AFS/SEARCH/afs_replyset_helper.php';
require_once 'AFS/SEARCH/afs_promote_replyset_helper.php';
require_once 'AFS/SEARCH/afs_spellcheck_helper.php';
require_once 'AFS/SEARCH/afs_concept_helper.php';
require_once 'AFS/SEARCH/afs_producer.php';
require_once 'AFS/SEARCH/afs_helper_configuration.php';
require_once 'AFS/SEARCH/afs_response_exception.php';
require_once 'COMMON/afs_helper_format.php';
require_once 'AFS/SEARCH/afs_metadata_helper.php';

/** @brief Main helper for AFS search reply.
 *
 * This helper is intended to be initiliazed with the reply provided by @a
 * AfsSearchQueryManager::send. It allows to manage replies of one of the
 * available replysets, including facets and pager. Connection and query errors
 * are managed in a uniform way to simplify integration.
 */
class AfsResponseHelper extends AfsResponseHelperBase
{
    private $config = null;
    private $has_reply = false;
    private $has_metadata = false;
    private $header = null;
    private $replysets = array();
    private $metadata = array();
    private $spellcheck_mgr = null;
    private $promote = null;
    private $concepts = null;

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
        $query = $query->auto_set_from();
        $this->config = $config;
        $this->header = new AfsHeaderHelper($response->header);

        if (property_exists($response, 'replySet')) {
            $this->has_reply = true;
            # remove cookie settings to avoid headers already send error
            #$us_mgr = $config->get_user_session_manager();
            #$us_mgr->set_user_id($this->header->get_user_id());
            #$us_mgr->set_session_id($this->header->get_session_id());

            $this->spellcheck_mgr = new AfsSpellcheckManager($query, $config);
            $this->concepts = new AfsConceptManager();

            $this->initialize_replysets($response->replySet, $query, $config);
        } elseif (property_exists($response, 'metadata')) {
            $this->has_metadata = true;
            $this->initialize_metadata($response->metadata);
        } elseif ($this->header->in_error()) {
            $this->set_error_msg($this->header->get_error());
        }
    }

    private function initialize_metadata($metadata) {
        foreach ($metadata as $meta) {
            $this->metadata[$meta->uri] = new AfsMetadataHelper($meta);
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
                        $this->promote = new AfsPromoteReplysetHelper($replyset,$this->config);
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

    /** @brief Check whether reponse has a reply. Possible to check reply for on feed or for all feeds
     * @param $feed name to be checked
     * @return true when a reply is available, false otherwise.
     */
    public function has_replyset($feed=null)
    {
        if ($feed == null)
            return $this->has_reply && (! empty($this->replysets));
        else {
            try {
                $this->get_replyset($feed);
                return true;
            } catch (OutOfBoundsException $e) {
                return false;
            } catch (AfsNoReplyException $e) {
                return false;
            }
        }
    }
    /** @brief Retrieves all replysets.
     * @return all defined reply sets.
     */
    public function get_replysets()
    {
        $this->check_reply('replysets');
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
        $this->check_reply('replysets');
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
        return $this->has_reply and (!is_null($this->spellcheck_mgr))
        and $this->spellcheck_mgr->has_spellcheck();
    }
    /** @brief Retrieves spellchecks from the @a response.
     * @return list of @a AfsSpellcheckHelper or formatted spellcheck depending
     * on parameter initialization.
     */
    public function get_spellchecks()
    {
        $this->check_reply('spellcheck_mgr');
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
        $this->check_reply('spellcheck_mgr');
        return $this->spellcheck_mgr->get_spellcheck($feed);
    }

    /** @brief Retrieves metadata for all feeds */
    public function get_all_metadata() {
        return $this->metadata;
    }

    /**
     * @brief Retrieves metadata for given feed
     * @param $feed
     */
    public function get_feed_metadata($feed=null) {
        return $this->metadata[$feed];
    }

    /**@brief Check if metadata is defined in response stream
     * @param $feed. If not null, check if metadata is defined for this feed.
     *               If null, check if at least one feed contains metadata.
     * @return bool
     */
    public function has_metadata($feed=null)
    {
        if (! is_null($feed)) {
            return array_key_exists($feed, $this->metadata);
        } else {
            return $this->has_metadata;
        }
    }
    /** @} */

    /** @name Promote
     * @{ */

    /** @brief Checks whether at least one promote is available.
     * @return @c True when one or more promotes is available, @c false otherwise.
     */
    public function has_promote()
    {
        return $this->has_reply and (! is_null($this->promote))
        and $this->promote->has_reply();
    }
    /** @brief Retrieves all promote helpers.
     * @return promote replies.
     */
    public function get_promotes()
    {
        $this->check_reply('promote');
        return $this->promote->get_replies();
    }
    /** @brief Retrieves promote replyset helper.
     *
     * This allows to retrieve metadata of the promote.
     * For more details see @a AfsPromoteReplysetHelper.
     *
     * @return promote replyset helper.
     */
    public function get_promote()
    {
        $this->check_reply('promote');
        return $this->promote;
    }
    /** @} */

    /** @name Concept
     * @{ */

    /** @brief Checks whether at least one concept is available.
     * @return @c True when one or more concepts is available, @c false otherwise.
     */
    public function has_concept()
    {
        return $this->has_reply and (! is_null($this->concepts))
        and $this->concepts->has_concept();
    }
    /** @brief Retrieves all concept helpers.
     * @return concept replies.
     */
    public function get_concepts()
    {
        $this->check_reply('concepts');
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
        $this->check_reply('concepts');
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

    /** @brief Retrieves reply data as array.
     *
     * This method is intended for internal use only.
     *
     * All data are stored in <tt>key => value</tt> format:
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


    private function check_reply($param=null)
    {
        if (! $this->has_reply)
            throw new AfsNoReplyException('No reply available!');
        if (! is_null($param) && is_null($this->$param))
            throw new AfsNoReplyException('No ' . $param . ' reply available!');
    }

     /** @brief Retrieve query parameter stored in header
      * @input $key : Name of the parameter
      * @return value of the parameter
      *
      */
    public function get_query_parameter($key)
    {
        return $this->header->get_query_parameter($key);
    }

    /**
     * @brief get request orchestration type (AutoSpellchecker or fallbackToOptional)
     * @return OrchestrationType::(AutoSpellchecker or fallbackToOptional)
     * @throws Exception
     * @throws OrchestrationTypeException
     */
    public function get_orchestration_type()
    {
        return $this->header->get_orchestration_type();
    }

    /**
     * @return true if this request is a result of orchestration
     */
    public function is_orchestrated()
    {
        return $this->header->is_orchestrated();
    }
    /** @} */

}


