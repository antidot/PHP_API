<?php
require_once 'COMMON/afs_helper_base.php';
require_once 'AFS/ACP/afs_acp_reply_helper.php';
require_once 'AFS/ACP/afs_acp_exception.php';

/** @brief AFS ACP Replyset helper.
 *
 * Each replyset corresponds to suggestions of each feed.
 */
class AfsAcpReplysetHelper extends AfsHelperBase
{
    private $name = null;   ///> Name of the feed which has produced these suggestions.
    private $query_string = null;
    private $replies = array();

    /** @brief Constructs new ACP replyset helper.
     *
     * @param $name [in] Feed name source of generated suggestions.
     * @param $reply_set [in] Json decoded suggestions of specific replyset.
     * @param $config ACP configuration.
     *
     * @exception AfsAcpEmptyReplysetException when feed does not generate any
     *            suggestion. 
     * @exception AfsAcpUnmanagedSuggestionFormatException when provided
     *            $reply_set format is unknown.
     * @exception AfsAcpInvalidSuggestionFormatException when provided
     *            $reply_set is invalid.
     *            
     */
    public function __construct($name, $reply_set, AfsAcpConfiguration $config=null)
    {
        $size = count($reply_set);
        if ($size < 2 || $size > 3)
            throw new AfsAcpUnmanagedSuggestionFormatException();

        $this->query_string = reset($reply_set);
        $suggestions = next($reply_set);
        if (empty($suggestions))
            throw new AfsAcpEmptyReplysetException($this->query_string);

        if ($size == 3) {
            $metas = next($reply_set);
            if (count($metas) != count($suggestions))
                throw new AfsAcpInvalidSuggestionFormatException();
        } else {
            $metas = array_fill(0, count($suggestions), null);
        }

        $this->name = $name;
        for ($i = 0; $i < count($suggestions); $i++)
            $this->replies[] = new AfsAcpReplyHelper($suggestions[$i], $metas[$i], $config);
    }

    /** @brief Retrieves name of the feed which has produced these suggestions.
     * @return feed name.
     */
    public function get_feed()
    {
        return $this->name;
    }

    /** @brief Retrieves query string.
     * @return query string.
     */
    public function get_query_string()
    {
        return $this->query_string;
    }

    /** @name Suggestions
     * @{ */

    /** @brief Checks wether at least one reply is available.
     * @return @c true when one or more replies are available, @c false
     *         otherwise.
     */
    public function has_reply()
    {
        return (empty($this->replies) ? false : true);
    }
    /** @brief Retrieves number suggestions.
     * @return Number of suggestions for current feed.
     */
    public function get_nb_replies()
    {
        return count($this->replies);
    }

    /** @brief Retrieves suggestions.
     * @return List of suggestion helpers.
     */
    public function get_replies()
    {
        return $this->replies;
    }
    /** @} */

    /** @name Miscellaneous
     * @{ */

    /** @brief Retrieves suggestions as array.
     *
     * This method is intended for internal use only.
     *
     * All suggestions are stored in list format.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        $replies = array();
        foreach ($this->replies as $reply)
            $replies[] = $reply->format();
        return array('feed' => $this->name,
                     'replies' => $replies);
    }
    /** @} */
}
