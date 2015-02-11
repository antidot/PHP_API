<?php
require_once 'COMMON/afs_user_session_manager.php';
require_once 'COMMON/afs_exception.php';
require_once 'AFS/afs_origin.php';
require_once 'AFS/afs_single_value_parameter.php';
require_once 'AFS/afs_multiple_values_parameter.php';
require_once 'AFS/afs_feed.php';

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
    protected $custom_parameters = array();


    /** @brief Constructs new AFS ACP query object.
     * @param $afs_query [in] instance used for initialization (defaul: create
     *        new empty instance).
     */
    public function __construct(AfsQueryBase $afs_query=null)
    {
        if (is_null($afs_query)) {
            $this->userId = new AfsSingleValueParameter('userId', uniqid('user_'));
            $this->sessionId = new AfsSingleValueParameter('sessionId', uniqid('session_'));
            $this->replies = new AfsSingleValueParameter('replies', 10);
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
            $this->custom_parameters = $afs_query->custom_parameters;
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
    public function has_feed($feed=null)
    {
        if (is_null($feed)) {
            foreach ($this->feed as $f) {
                if ($f->is_activated())
                    return true;
            }
            return false;
        }
        else {
            foreach ($this->feed as $f) {
                if ($f->is_activated() && $f->get_name() === $feed)
                    return true;
            }
            return false;
        }
    }

    /** @brief Assigns new feed name replacing any existing one.
     * @param $feed [in] new feed to filter on.
     */
    public function set_feed($feed)
    {
        $copy = $this->copy();
        is_null($assignment_res = $copy->on_assignment()) ? null : $copy = $assignment_res;
        $copy->feed = array(new AfsFeed($feed, true));
        return $copy;
    }

    /** @brief Assigns new feed name.
     * @param $feed [in] new feed to filter on.
     */
    public function add_feed($feed)
    {
        $copy = $this->copy();
        is_null($assignment_res = $copy->on_assignment()) ? null : $copy = $assignment_res;

        $feed_found = false;
        foreach ($this->feed as $f) {
            if ($f->get_name() === $feed) {
                $f->set_activated(true);
                $feed_found = true;
                break;
            }
        }

        if (! $feed_found) {
            $copy->feed[] = new AfsFeed($feed, true);
        }

        return $copy;
    }

    protected function get_feed($feed) {
        foreach ($this->feed as $f) {
            if ($f->get_name() === $feed) {
                return $f;
            }
        }
        return null;
    }

    /** @brief Retrieves all defined feeds.
     * @return defined feeds in array or null when no feed is defined.
     */
    public function get_feeds()
    {
        $feeds = array();
        foreach ($this->feed as $feed) {
            $feeds[] = $feed->get_name();
        }
        return $feeds;
    }
    /**  @} */

    /** @name Query management
     * @{ */

    /** @brief Checks whether current instance has a query.
     * @return true when a query is defined, false otherwise.
     */
    public function has_query($feed=null)
    {
        return $this->has_parameter('query', $feed);
    }
    /** @brief Retrieves the query.
     *
     * You should have previously tested whether the instance has a defined
     * query by calling @a has_query.
     * @return the query.
     */
    public function get_query($feed=null)
    {
        return $this->get_parameter('query', $feed);
    }
    /** @brief Assigns new query value.
     *
     * Any previously defined query is replaced by the provided one.
     * @param $new_query [in] query to assign to the instance.
     * @return new up to date instance.
     */
    public function set_query($new_query, $feed=null)
    {
        $copy = $this->copy();
        is_null($assignment_res = $copy->on_assignment()) ? null : $copy = $assignment_res;

        $copy->set_parameter('query', $new_query, $feed);

        return $this->auto_set_from ? $copy->set_from(AfsOrigin::SEARCHBOX) : $copy;
    }
    /**  @} */

    /** @name Number of replies management
     * @{ */

    /** @brief Checks whether replies is set.
     * @return always true.
     */
    public function has_replies($feed=null)
    {
        return $this->has_parameter('replies', $feed);
    }
    /** @brief Defines new number of replies.
     * @param $replies_nb [in] requested number of replies. It should be
     *        greater than or equal to 1.
     * @return new up to date instance.
     * @exception Exception on invalid replies number provided.
     */
    public function set_replies($replies_nb, $feed=null)
    {
        $copy = $this->copy();
        is_null($assignment_res = $copy->on_assignment()) ? null : $copy = $assignment_res;

        $copy->set_parameter('replies', $replies_nb, $feed);

        return $this->auto_set_from ? $copy->set_from(AfsOrigin::SEARCHBOX) : $copy;
    }
    /** @brief Get number of replies per page.
     * @return number of replies per reply page.
     */
    public function get_replies($feed=null)
    {
        return $this->get_parameter('replies', $feed);
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
    public function set_from($from, $feed=null)
    {
        AfsOrigin::check_value($from, 'Invalid query origin: ');
        $this->set_parameter('from', $from, $feed);
        return $this;
    }
    /** @brief Retrieves origin of the query.
     * @return origin of the query.
     */
    public function get_from($feed=null)
    {
        return $this->get_parameter('from', $feed);
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
        $this->set_parameter('userId', $user_id);
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
        return $this->get_parameter('userId');
    }

    /** @brief Defines session id.
     * @remark Page value is preserved when this method is called.
     * @param $session_id [in] Session id to set.
     * @return current instance.
     */
    public function set_session_id($session_id)
    {
        $this->set_parameter('sessionId', $session_id);
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
        return $this->get_parameter('sessionId');
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
        $this->log[] = new AfsSingleValueParameter('log', $value);
        return $this;
    }

    /** @brief Retrieves current defined logs.
     *
     * Main purpose of this method is debugging purpose.
     * @return defined logs.
     */
    public function get_logs()
    {
        $logs = array();
        foreach ($this->log as $log) {
            $logs[] = $log->get_value();
        }

        return $logs;
    }
    /** @} */

    /** @name Key management
     * @{ */

    /** @brief Checks whether key parameter is set.
     * @return True when key parameter is set, false otherwise.
     */
    public function has_key($feed=null)
    {
        return $this->has_parameter('key', $feed);
    }
    /** @brief Defines key id.
     * @param $key_id [in] Key id to set.
     * @return Current instance.
     */
    public function set_key($key_id, $feed=null)
    {
        $this->set_parameter('key', $key_id, $feed);
        return $this;
    }
    /** @brief Retrieves key value.
     * @return Key value.
     */
    public function get_key($feed=null)
    {
        return $this->get_parameter('key', $feed);
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
            $own_param = $this->$param;
            if ($own_param != null && !empty($own_param)) {
                if (is_object($own_param) && is_callable(array($own_param, 'format')))
                    $own_param = $own_param->format();
                elseif (is_array($own_param)) {
                    $formatted_param = array();
                    foreach ($own_param as $key => $param_value) {
                        if (is_object($param_value) && is_callable(array($param_value, 'format'))) {
                            $formatted_value = $param_value->format();
                            if (is_array($formatted_value))
                                $formatted_param = array_merge($formatted_param, $formatted_value);
                            else
                                $formatted_param[$key] = $formatted_value;
                        } else {
                            $formatted_param[$key] = $param_value;
                        }
                    }
                    $own_param = $formatted_param;
                }
                $result[$param] = $own_param;
            }
        }

        $result = $this->add_feed_parameters($parameters, $result);
        return $result;
    }

    private function add_feed_parameters(array $parameters_to_add, array $parameters) {
        foreach($this->feed as $feed) {
            if ($feed->is_activated()) {
                if (! array_key_exists('feed', $parameters))
                    $parameters['feed'] = array();

                $parameters['feed'][] = $feed->get_name();
            }

            $feed_parameters = $feed->get_parameters($parameters_to_add);
            /* feed parameter should override default parameter
            foreach ($feed_parameters as $name => $parameter) {
                list($name, $feed) = explode('@', $name);
                if (array_key_exists($name, $parameters) === true) {
                    unset($parameters[$name]);
                }
            }*/

            $parameters = array_merge($parameters, $feed_parameters);
        }

        return $parameters;
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
        return array_merge(array('replies', 'query'), array_keys($this->custom_parameters));
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

    public function get_custom_parameters()
    {
        return $this->custom_parameters;
    }

    public function set_custom_parameter($key, $value, $feed=null)
    {
        $this->custom_parameters[$key] = $value;
    }

    /** @brief Add ability to get custom params as member fields */
    public function __get($name)
    {
        if(property_exists($this, $name)) {
            //$name is a field of AfsQueryBase
            return $this->$name;
        } else {
            if (array_key_exists($name, $this->custom_parameters)) {
                //$name is a custom parameter
                return $this->custom_parameters[$name];
            } else {
                throw new InvalidArgumentException();
            }
        }
    }


    protected function has_parameter($param, $feed=null)
    {
        if (! is_null($feed) && ($f = $this->get_feed($feed)) !== null) {
            return $f->get_parameter($param) == null ? false : true;
        } else {
            return $this->$param != null;
        }
    }

    protected function get_parameter($param, $feed=null)
    {
        if (! is_null($feed) && ($f = $this->get_feed($feed)) != null) {
            return $f->get_parameter($param)->get_value();
        } else {
            if (is_null($this->$param)) {
                return null;
            } else {
                return $this->$param->get_value();
            }
        }
    }

    protected function set_parameter($param, $value, $feed=null) {
        if (! is_null($feed) &&  ($f = $this->get_feed($feed)) !== null) {
            if (($q = $f->get_parameter($param)) !== null) {
                $f->get_parameter($param)->set_value($value);
            } else {
                $f->add_parameters(new AfsSingleValueParameter($param, $value));
            }
        } elseif (! is_null($feed)) {
            $f = new AfsFeed($feed, false);
            $f->add_parameters(new AfsSingleValueParameter($param, $value));
            $this->feed[] = $f;
        } else {
            $this->$param = new AfsSingleValueParameter($param, $value);
        }
    }
}
