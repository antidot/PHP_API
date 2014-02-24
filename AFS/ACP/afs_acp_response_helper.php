<?php
require_once 'AFS/ACP/afs_acp_exception.php';
require_once 'AFS/ACP/afs_acp_replyset_helper.php';
require_once 'AFS/afs_response_helper_base.php';


/** @brief Main helper for AFS ACP reply.
 *
 * This helper is intended to be initialized with reply from @a
 * AfsAcpQueryManager::send. It allows to manage suggestions from one or
 * more feeds.
 */
class AfsAcpResponseHelper extends AfsResponseHelperBase
{
    private $replysets = array();
    private $query_string = null;


    /** @brief Constructs new ACP response helper.
     *
     * @param $response [in] Json decoded reply.
     * @param $config [in] ACP configuration.
     */
    public function __construct($response, AfsAcpConfiguration $config=null)
    {
        if (array_key_exists('error', $response)) {
            $this->set_error_msg($response['error']);
            return;
        }

        if (array_values($response) === $response)
            $response = array('' => $response);
        foreach ($response as $feed => $replies) {
            try {
                $replyset = new AfsAcpReplysetHelper($feed, $replies, $config);
                $this->replysets[$feed] = $replyset;
            } catch (AfsAcpEmptyReplysetException $e) {
                $this->query_string = $e->get_query_string();
            }
        }
        if (is_null($this->query_string) && ! empty($this->replysets)) {
            $replyset = reset($this->replysets);
            $this->query_string = $replyset->get_query_string();
        }
    }


    /** @brief Retrieves query string.
     * @return query string.
     */
    public function get_query_string()
    {
        return $this->query_string;
    }

    /** @name Replies
     * @{ */

    /** @brief Checks whether there is suggestion.
     * @return @c true when at least one suggestion is available, @c false
     * otherwise.
     */
    public function has_replyset()
    {
        return (! $this->in_error()) && (! empty($this->replysets));
    }
    /** @brief Retrieves all suggestions from all feeds.
     *
     * Map of suggestions where the key corresponds to the name of feed which
     * has generated suggestions and the value corresponds to suggestion helper.
     *
     * @return all defined suggestions per feed.
     */
    public function get_replysets()
    {
        return $this->replysets;
    }
    /** @brief Retrieves suggestions from specific feed.
     *
     * @param $feed [in] name of the feed to filter on
     *        (default: empty string -> retrieves generic spellcheck if any,
     *        otherwise raise exception)
     * @return @a AfsReplysetHelper or formatted replyset depending on @a format
     * parameter.
     * @exception OutOfBoundsException when required feed does not exist. This
     *            also happen when no feed name is provided whereas all
     *            suggestions are associated to named feeds.
     */
    public function get_replyset($feed='')
    {
        if (array_key_exists($feed, $this->replysets))
            return $this->replysets[$feed];
        else
            throw new OutOfBoundsException('No spellcheck available'
                . (empty($feed) ? '' : ' for feed named: ' . $feed));
    }
    /** @} */

    /** @name Miscellaneous
     * @{ */

    /** @brief Retrieves suggestions as array.
     *
     * This method is intended for internal use only.
     *
     * All data are stored in <tt>key => value</tt> format:
     * @li <tt>feed name</tt>: suggestions per feed,
     * Or, in case of error:
     * @li @c error: error message.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        if ($this->in_error()) {
            return array('error' => $this->get_error_msg());
        } else {
            $result = array('query_string' => $this->query_string);
            foreach ($this->replysets as $feed => $replyset)
                $result[$feed] = $replyset->format();
            return $result;
        }
    }
    /** @} */

}
