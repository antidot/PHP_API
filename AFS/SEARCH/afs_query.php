<?php
require_once "COMMON/afs_language.php";
require_once "AFS/SEARCH/afs_origin.php";

/** @brief Represent an AFS query.
 *
 * All instances of this class are immutable: each method call involves
 * creation of new instance copied from current one. The newly created instance
 * is modified according to the called method and returned.
 * So, <b>do not forget</b> to store returned object.
 * @code
 * $query = new AfsQuery();
 * $query->set_query('my query');
 * if (! $query->has_query())
 * {
 *   echo 'You do not save the result of set_query!';
 * }
 * @endcode
 */

/** @internal
 * key, user, group
 */
class AfsQuery
{
    private $feed = array();    // afs:feed
    private $query = null;      // afs:query
    private $filter = array();  // afs:filter
    private $page = 1;          // afs:page
    private $replies = 10;      // afs:replies
    private $lang = null;       // afs:lang
    private $sort = null;       // afs:sort
    private $from = null;       // afs:from : query origin
    private $userId = null;     // afs:userId
    private $sessionId = null;  // afs:sessionId
    private $facetDefault = 'replies=1000'; // afs:facetDefault
    private $log = array();     // afs:log
    private $key = null;

    /**
     * @brief Construct new AFS query object.
     * @param $afs_query [in] instance used for initialization (default:
     *        create new empty instance).
     */
    public function __construct(AfsQuery $afs_query = null)
    {
        if ($afs_query != null) {
            $this->feed = $afs_query->feed;
            $this->query = $afs_query->query;
            $this->filter = $afs_query->filter;
            $this->page = $afs_query->page;
            $this->replies = $afs_query->replies;
            $this->lang = $afs_query->lang;
            $this->sort = $afs_query->sort;
            $this->from = $afs_query->from;
            $this->userId = $afs_query->userId;
            $this->sessionId = $afs_query->sessionId;
            $this->log = $afs_query->log;
            $this->key = $afs_query->key;
        } else {
            $this->lang = new AfsLanguage(null);
        }
    }

    /** @internal
     * @brief Copy current instance.
     * @return New copied instance.
     */
    private function copy()
    {
        return new AfsQuery($this);
    }

    /** @name Feed management
     * @{ */

    /** @brief Check whether feed parameter is set.
     * @return true when at least one feed is defined, false otherwise.
     */
    public function has_feed()
    {
        return ! empty($this->feed);
    }

    /** @brief Assign new feed name replacing any existing one.
     * @param $feed [in] new feed to filter on.
     */
    public function set_feed($feed)
    {
        $copy = $this->copy();
        $copy->reset_page();
        $copy->feed = array($feed);
        return $copy;
    }

    /** @brief Assign new feed name.
     * @param $feed [in] new feed to filter on.
     */
    public function add_feed($feed)
    {
        $copy = $this->copy();
        $copy->reset_page();
        $copy->feed[] = $feed;
        return $copy;
    }

    /** @brief Retrieve all defined feeds.
     * @return defined feeds in array or null when no feed is defined.
     */
    public function get_feeds()
    {
        return $this->feed;
    }
    /**  @} */


    /** @name Query management
     * @{ */

    /** @brief Check whether current instance has a query.
     * @return true when a query is defined, false otherwise.
     */
    public function has_query()
    {
        return $this->query != null;
    }
    /** @brief Retrieve the query.
     *
     * You should have previously tested whether the instance has a defined
     * query by calling @a has_query.
     * @return the query.
     */
    public function get_query()
    {
        return $this->query;
    }
    /** @brief Assign new query value.
     *
     * Any previously defined query is replaced by the provided one.
     * @param $new_query [in] query to assign to the instance.
     * @return new up to date instance.
     */
    public function set_query($new_query)
    {
        $copy = $this->copy();
        $copy->reset_page();
        $copy->query = $new_query;
        return $copy->set_from(AfsOrigin::SEARCHBOX);
    }
    /**  @} */

    /** @name Filter management
     * @{ */

    /** @brief Assign new value to specific facet replacing any existing one.
     * @param $facet_id [in] id of the facet to update.
     * @param $value [in] new value to filter on.
     * @return new up to date instance.
     */
    public function set_filter($facet_id, $value)
    {
        $copy = $this->copy();
        $copy->reset_page();
        $copy->filter[$facet_id] = array($value);
        return $copy->set_from(AfsOrigin::FACET);
    }
    /** @brief Assign new value to specific facet.
     * @param $facet_id [in] id of the facet for which new @a value should be
     *        added.
     * @param $value [in] value to add to the facet.
     * @return new up to date instance.
     */
    public function add_filter($facet_id, $value)
    {
        $copy = $this->copy();
        $copy->reset_page();
        if (empty($copy->filter[$facet_id]))
        {
            $copy->filter[$facet_id] = array();
        }
        $copy->filter[$facet_id][] = $value;
        return $copy->set_from(AfsOrigin::FACET);
    }
    /** @brief Remove existing value from specific facet.
     * @remark No error is reported when the removed @a value is not already set.
     * @param $facet_id [in] id of the facet to update.
     * @param $value [in] value to be removed from the list of values associated
     *        to the facet.
     * @return new up to date instance.
     */
    public function remove_filter($facet_id, $value)
    {
        $copy = $this->copy();
        $copy->reset_page();
        if (! empty($copy->filter[$facet_id]))
        {
            $pos = array_search($value, $copy->filter[$facet_id]);
            unset($copy->filter[$facet_id][$pos]);
            if (empty($copy->filter[$facet_id]))
            {
                unset($copy->filter[$facet_id]);
            }
        }
        return $copy->set_from(AfsOrigin::FACET);
    }
    /** @brief Check whether instance has a @a value associated with specified
     * facet id.
     * @param $facet_id [in] id of the facet to check.
     * @param $value [in] value to check in the list of values for the given
     *        @a facet_id.
     * @return true when the @a value is present in the list of values
     * associated with @a facet_id, false otherwise. Always false when provided
     * @a facet_id is unknown.
     */
    public function has_filter($facet_id, $value)
    {
        if (empty($this->filter[$facet_id]))
        {
            return false;
        }
        else
        {
            if (! isset($value))
            {
                return true;
            }
            else
            {
                return in_array($value, $this->filter[$facet_id]);
            }
        }
    }
    /** @brief Retrieve the list of values for specific facet id.
     * @remark You should ensure that the required @a facet_id is valid.
     * @param $facet_id [in] facet id to consider.
     * @return list of values associated to the given @a facet_id.
     */
    public function get_filter_values($facet_id)
    {
        return $this->filter[$facet_id];
    }
    /** @brief Retrieve the list of all managed facet ids.
     *
     * Only elements from this list should be used to query @a get_filter_values
     * method.
     * @return list of facet ids.
     */
    public function get_filters()
    {
        return array_keys($this->filter);
    }
    /**  @} */

    /** @name Page management
     * @{ */

    /** @brief Check whether reply page is set.
     * @return always true.
     */
    public function has_page()
    {
        return $this->page != null;
    }
    /** @brief Define new result page.
     * @param $page [in] result page to output. It should be greater than or
     *        equal to 1.
     * @return new up to date instance.
     * @exception Exception on invalid page number.
     */
    public function set_page($page)
    {
        if ($page <= 0)
        {
            throw new Exception('Invalid page number: ' . $page);
        }
        $copy = $this->copy();
        $copy->page = $page;
        return $copy->set_from(AfsOrigin::PAGER);
    }
    /** @brief Retrieve current reply page.
     * @remark For a new query, this vaue is reset to 1.
     * @return reply page number.
     */
    public function get_page()
    {
        return $this->page;
    }
    /** @brief Shortcut for @a set_page(1).
     *
     * This method do not copy the instance and change current one inplace.
     */
    protected function reset_page()
    {
        return $this->page = 1;
    }
    /**  @} */

    /** @name Replies per page management
     * @{ */

    /** @brief Check whether replies is set.
     * @return always true.
     */
    public function has_replies()
    {
        return $this->replies != null;
    }
    /** @brief Define new number of reply per page.
     * @param $replies_nb [in] requested replies per reply page. It should be
     *        greater than or equal to 1.
     * @return new up to date instance.
     * @exception Exception on invalid replies number provided.
     */
    public function set_replies($replies_nb)
    {
        if ($replies_nb < 0)
        {
            throw new Exception('Invalid number of replies per page: ' . $replies_nb);
        }
        $copy = $this->copy();
        $copy->reset_page();
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

    /** @name Language management
     * @{ */

    /** @brief Check whether language is set.
     * @return true when language parameter is set, false otherwise.
     */
    public function has_lang()
    {
        return $this->lang->lang != null;
    }
    /** @brief Remove filter on language.
     */
    public function reset_lang()
    {
        return $this->set_lang(null);
    }
    /** @brief Define new language.
     *
     * See @a AfsLanguage for more details on valid values.
     *
     * @param $lang [in] New language to filter on. Empty string or null value
     *        resets current language filter.
     * @exception Exception when provided language is invalid.
     */
    public function set_lang($lang)
    {
        $lang = new AfsLanguage($lang);
        $copy = $this->copy();
        $copy->reset_page();
        $copy->lang = $lang;
        return $copy;
    }
    /** @brief Retrieve current language filter.
     * @return language filter or null when no language is set.
     */
    public function get_lang()
    {
        return $this->lang;
    }
    /**  @} */

    /** @name Sort order management
     * @{ */

    /** @brief Check whether sort parameter is set.
     * @return true when sort parameter is set, false otherwise.
     */
    public function has_sort()
    {
        return $this->sort != null;
    }
    /** @brief Reset sort order to AFS default sort order.
     */
    public function reset_sort()
    {
        return $this->set_sort(null);
    }
    /** @brief Define new sort order.
     *
     * Provided sort order must conform following syntax:
     * <tt>facet1,ORDER;facet2,ORDER;...</tt>
     * where:
     * - @c facetX: should be a built-in facet like: @c afs:weight,
     *              @c afs:relevance, @c afs:words ... or user defined facet
     * - @c ORDER: should be @c ASC or @c DESC for ascending and descending
     *             order respectively. Order can be omitted.
     *
     * @param $sort_order [in] new sort order. When set to emty string or null,
     *        this call to this method is equivalent to call to @a reset_sort.
     *
     * @exception Exception when provided sort order do not conform to required
     * syntax.
     *
     * @internal
     * We can provide a simplest interface to define sort parameter.
     */
    public function set_sort($sort_order)
    {
        if ($sort_order == '')
        {
            $sort_order = null;
        }
        $copy = $this->copy();
        $copy->reset_page();
        $copy->sort = $sort_order;
        return $copy;
    }
    /** @brief Retrieve sort order.
     * @return sort order.
     */
    public function get_sort()
    {
        return $this->sort;
    }
    /**  @} */

    /** @name Origine of the query.
     * @{ */

    /** @brief Defines the origin of the query.
     *
     * @remark Page value is preserved when this method is called.
     *
     * @param $from [in] origin of the query. It should be a value defined by
     *        @a AfsOrigin.
     *
     * @return current instance.
     * @exception Exception when provided origin value is invalid.
     */
    public function set_from($from)
    {
        if (AfsOrigin::is_valid_value($from)) {
            $this->from = $from;
        } else {
            throw new Exception('Invalid query origin: ' . $from);
        }
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
     *
     * The value of the session id is reset as soon as new user id is set.
     * @{ */

    /** @brief Defines user id.
     * @remark Session id is reset as soon as new value is defined for user id.
     * @remark Page value is preserved when this method is called.
     * @param $user_id [in] User id to set.
     * @return current instance.
     */
    public function set_user_id($user_id)
    {
        if ($this->userId != $user_id) {
            $this->userId = $user_id;
            $this->set_session_id(null);
        }
        return $this;
    }
    /** @brief Checks whether user id is set.
     * @return @c True when user id is set, @c false otherwise.
     */
    public function has_user_id()
    {
        return ! is_null($this->userId);
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
     * @return @c True when session id is set, @c false otherwise.
     */
    public function has_session_id()
    {
        return ! is_null($this->sessionId);
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
     * @a AfsUserSessionManager for more details.
     *
     * @param $mgr [in] Instance of @a AfsUserSessionManager.
     */
    public function initialize_user_and_session_id(AfsUserSessionManager $mgr)
    {
        $this->set_user_id($mgr->get_user_id());
        $this->set_session_id($mgr->get_session_id());
    }

    /** @brief Update user and session identifiers.
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

    /** @name Full configuration through array of parameters
     * @{ */

    /** @brief Create full query from array of parameters
      * @param $params [in] structured array of parameters.
      * @return correctly initialized query.
     */
    public static function create_from_parameters(array $params)
    {
        uksort($params, function($a, $b) { return $a == 'page' ? 1 : 0; });

        $result = new AfsQuery();
        foreach ($params as $param => $values) {
            $adder = 'add_' . $param;
            $setter = 'set_' . $param;
            if ($param == 'filter') {
                foreach ($values as $filter => $filter_values) {
                    foreach ($filter_values as $value) {
                        $result = $result->add_filter($filter, $value);
                    }
                }
            } elseif (method_exists($result, $adder)) {
                foreach ($values as $value) {
                    $result = $result->$adder($value);
                }
            } elseif (method_exists($result, $setter)) {
                $result = $result->$setter($values);
            } else {
                throw new InvalidArgumentException('Cannot initialize '
                    . 'query: unknown parameter ' . $param);
            }
        }
        return $result;
    }

    /** @brief Retrieves query parameters.
     *
     * Retrieved all or relevant parameters. For example, @c from parameter is
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
        $parameters = array('feed', 'query', 'filter', 'sort');
        if ($all) {
            array_push($parameters, 'from', 'userId', 'sessionId', 'facetDefault', 'log', 'key');
        }

        $result = array('replies' => $this->replies);
        if ($this->page != 1) {
            $result['page'] = $this->page;
        }
        foreach ($parameters as $param) {
            if ($this->$param != null && !empty($this->$param)) {
                $result[$param] = $this->$param;
            }
        }
        if (! is_null($this->lang->lang)) {
            $result['lang'] = $this->lang;
        }
        return $result;
    }
    /**  @} */
}

?>
