<?php
require_once 'COMMON/afs_user_session_manager.php';
require_once 'COMMON/afs_exception.php';
require_once 'AFS/SEARCH/afs_origin.php';

/** @brief Represents an AFS query.
 *
 * Derived class of this one:
 * - must implement copy method: so that AfsQueryXXX objects can be immutable.
 * - should reimplement on_assignment method: to trigger specific actions when
 *   an assignment occurs.
 */
abstract class AfsQueryBase
{
    protected $feed = array();      // afs:feed
    protected $query = null;        // afs:query
    protected $replies = 10;        // afs:replies
    protected $userId = null;       // afs:userId
    protected $sessionId = null;    // afs:sessionId
    protected $log = array();       // afs:log
    protected $key = null;          // afs:key
    protected $from = null;         // afs:from : query origin
    protected $auto_set_from = true;


    /** @brief Constructs new AFS ACP query object.
     * @param $afs_query [in] instance used for initialization (defaul: create
     *        new empty instance).
     */
    public function __construct(AfsQueryBase $afs_query=null)
    {
        if (is_null($afs_query)) {
            $this->userId = uniqid('user_');
            $this->sessionId = uniqid('session_');
        } else {
            $this->feed = $afs_query->feed;
            $this->query = $afs_query->query;
            $this->replies = $afs_query->replies;
            $this->userId = $afs_query->userId;
            $this->sessionId = $afs_query->sessionId;
            $this->log = $afs_query->log;
            $this->key = $afs_query->key;
            $this->from = $afs_query->from;
            $this->auto_set_from = $afs_query->auto_set_from;
        }
    }

    protected  function copy()
    {
        throw new AfsNotImplementedException();
    }

    /** @brief Action to perform when an assignment occurs.
     *
     * Nothing is done unless this method is overloaded in child classes. */
    protected function on_assignment()
    { }

    /** @name Feed management
     * @{ */

    /** @brief Checks whether feed parameter is set.
     * @return @c true when at least one feed is defined, @c false otherwise.
     */
    public function has_feed()
    {
        return ! empty($this->feed);
    }

    /** @brief Assigns new feed name replacing any existing one.
     * @param $feed [in] new feed to filter on.
     */
    public function set_feed($feed)
    {
        $copy = $this->copy();
        $copy->on_assignment();
        $copy->feed = array($feed);
        return $copy;
    }

    /** @brief Assigns new feed name.
     * @param $feed [in] new feed to filter on.
     */
    public function add_feed($feed)
    {
        $copy = $this->copy();
        $copy->on_assignment();
        $copy->feed[] = $feed;
        return $copy;
    }

    /** @brief Retrieves all defined feeds.
     * @return defined feeds in array or null when no feed is defined.
     */
    public function get_feeds()
    {
        return $this->feed;
    }
    /**  @} */

    /** @name Query management
     * @{ */

    /** @brief Checks whether current instance has a query.
     * @return true when a query is defined, false otherwise.
     */
    public function has_query()
    {
        return $this->query != null;
    }
    /** @brief Retrieves the query.
     *
     * You should have previously tested whether the instance has a defined
     * query by calling @a has_query.
     * @return the query.
     */
    public function get_query()
    {
        return $this->query;
    }
    /** @brief Assigns new query value.
     *
     * Any previously defined query is replaced by the provided one.
     * @param $new_query [in] query to assign to the instance.
     * @return new up to date instance.
     */
    public function set_query($new_query)
    {
        $copy = $this->copy();
        $copy->on_assignment();
        $copy->query = $new_query;
        return $this->auto_set_from ? $copy->set_from(AfsOrigin::SEARCHBOX) : $copy;
    }
    /**  @} */

    /** @name Number of replies management
     * @{ */

    /** @brief Checks whether replies is set.
     * @return always true.
     */
    public function has_replies()
    {
        return $this->replies != null;
    }
    /** @brief Defines new number of replies.
     * @param $replies_nb [in] requested number of replies. It should be
     *        greater than or equal to 1.
     * @return new up to date instance.
     * @exception Exception on invalid replies number provided.
     */
    public function set_replies($replies_nb)
    {
        if ($replies_nb < 0)
            throw new Exception('Invalid number of replies: ' . $replies_nb);
        $copy = $this->copy();
        $copy->on_assignment();
        $copy->replies = $replies_nb;
        return $copy;
    }
    /** @brief Get number of replies per page.
     * @return number of replies per reply page.
     */
    public function get_replies()
    {
        return $this->replies;
    }
    /**  @} */

    /** @name Origin of the query
     * @{ */

    /** @brief Defines whether @c from parameter should be auto set when possible.
     * @param $auto_set [in] auto set status (default = @c true).
     * @return copy of the query with appropriate auto set parameter.
     */
    public function auto_set_from($auto_set=true)
    {
        $copy = $this->copy();
        $copy->auto_set_from = $auto_set;
        return $copy;
    }
    /** @brief Defines the origin of the query.
     *
     * @remark @c on_assignment method is not called by this method.
     *
     * @param $from [in] origin of the query. It should be a value defined by
     *        @a AfsOrigin.
     *
     * @return current instance.
     * @exception Exception when provided origin value is invalid.
     */
    public function set_from($from)
    {
        AfsOrigin::check_value($from, 'Invalid query origin: ');
        $this->from = $from;
        return $this;
    }
    /** @brief Retrieves origin of the query.
     * @return origin of the query.
     */
    public function get_from()
    {
        return $this->from;
    }
    /** @} */

    /** @name User and session identifier.
     *
     * These identifiers are used to uniquely identify a user and a
     * corresponding session.
     * @{ */

    /** @brief Defines user id.
     * @remark Page value is preserved when this method is called.
     * @param $user_id [in] User id to set.
     * @return current instance.
     */
    public function set_user_id($user_id)
    {
        $this->userId = $user_id;
        return $this;
    }
    /** @brief Checks whether user id is set.
     * @deprecated This method always return @c true and will be removed soon.
     * @return @c True when user id is set, @c false otherwise.
     */
    public function has_user_id()
    {
        return true;
    }
    /** @brief Retrieves user identifier.
     * @return user identifier or null if unset.
     */
    public function get_user_id()
    {
        return $this->userId;
    }

    /** @brief Defines session id.
     * @remark Page value is preserved when this method is called.
     * @param $session_id [in] Session id to set.
     * @return current instance.
     */
    public function set_session_id($session_id)
    {
        $this->sessionId = $session_id;
        return $this;
    }
    /** @brief Checks whether session id is set.
     * @deprecated This method always return @c true and will be removed soon.
     * @return @c True when session id is set, @c false otherwise.
     */
    public function has_session_id()
    {
        return true;
    }
    /** @brief Retrieves session id.
     * @return the session id or null if unset.
     */
    public function get_session_id()
    {
        return $this->sessionId;
    }

    /** @brief Initializes user id and session id.
     *
     * These identifiers are initialized thanks to specific manager. Refers to
     * @a AfsUserSessionManager for more details. User and session identifiers
     * are updated with identifiers from AfsUserSessionManager when they are
     * available. Otherwise, query identifiers are not modified.
     *
     * @param $mgr [in] Instance of @a AfsUserSessionManager.
     *
     * @return current instance.
     */
    public function initialize_user_and_session_id(AfsUserSessionManager $mgr)
    {
        $user_id = $mgr->get_user_id();
        if (! empty($user_id))
            $this->set_user_id($user_id);

        $session_id = $mgr->get_session_id();
        if (! empty($session_id))
            $this->set_session_id($session_id);

        return $this;
    }

    /** @brief Updates user and session identifiers.
     *
     * @deprecated Nothing happens since identifiers are always initialized.
     *
     * These identifiers are updated only if they are not yet defined.
     * @remark Page value is preserved when this method is called.
     *
     * @param $user_id [in] user id to set.
     * @param $session_id [in] session id to set.
     *
     * @return current instance.
     */
    public function update_user_and_session_id($user_id, $session_id)
    {
        if (! $this->has_user_id()) {
            $this->set_user_id($user_id);
        }
        if (! $this->has_session_id()) {
            $this->set_session_id($session_id);
        }
    }
    /** @} */

    /** @name Logging
     * @{ */

    /** @brief Adds new logging information.
     *
     * This can be used to log your system version or anything else.
     * Version of this API is appended to this list of values.
     * @remark Page value is preserved when this method is called.
     * @param $value [in] appended value.
     */
    public function add_log($value)
    {
        $this->log[] = $value;
        return $this;
    }

    /** @brief Retrieves current defined logs.
     *
     * Main purpose of this method is debugging purpose.
     * @return defined logs.
     */
    public function get_logs()
    {
        return $this->log;
    }
    /** @} */

    /** @name Key management
     * @{ */

    /** @brief Checks whether key parameter is set.
     * @return True when key parameter is set, false otherwise.
     */
    public function has_key()
    {
        return $this->key != null;
    }
    /** @brief Defines key id.
     * @param $key_id [in] Key id to set.
     * @return Current instance.
     */
    public function set_key($key_id)
    {
        $this->key = $key_id;
        return $this;
    }
    /** @brief Retrieves key value.
     * @return Key value.
     */
    public function get_key()
    {
        return $this->key;
    }
    /**  @} */


    /** @brief Retrieves query parameters.
     *
     * Retrieves all or relevant parameters. For example, @c from parameter is
     * not relevant except for AFS search engine. So, this parameter is not
     * retrieved when @a all is set to @c false.
     *
     * @param $all [in] if set to @c true, all parameters are retrieved,
     *        otherwise only relevent parameters are retrieved.
     *
     * @return array of all defined query parameters.
     */
    public function get_parameters($all=true)
    {
        $parameters = $this->get_aggregated_relevant_parameters();
        if ($all)
            $parameters = array_merge($parameters, $this->get_aggregated_additional_parameters());

        foreach ($parameters as $param) {
            if ($this->$param != null && !empty($this->$param))
                $result[$param] = $this->$param;
        }
        return $result;
    }

    private function get_aggregated_relevant_parameters()
    {
        return array_merge(AfsQueryBase::get_relevant_parameters(), $this->get_relevant_parameters());
    }

    private function get_aggregated_additional_parameters()
    {
        return array_merge(AfsQueryBase::get_additional_parameters(), $this->get_additional_parameters());
    }

    /** @brief Retrieves relevant parameters.
     *
     * Derived class should override this method with only its own parameters.
     * @return class specific relevant parameters.
     */
    protected function get_relevant_parameters()
    {
        return array('replies', 'feed', 'query');
    }

    /** @brief Retrieves additional parameters.
     *
     * Derived class should override this method with only its own parameters.
     * @return class specific additional parameters.
     */
    protected function get_additional_parameters()
    {
        return array('from', 'userId', 'sessionId', 'log', 'key');
    }
}
