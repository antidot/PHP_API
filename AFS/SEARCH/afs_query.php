<?php
require_once 'COMMON/afs_language.php';
require_once 'AFS/afs_query_base.php';
require_once 'AFS/SEARCH/afs_sort_order.php';
require_once 'AFS/SEARCH/afs_sort_builtins.php';

/** @brief Represent an AFS query.
 *
 * All instances of this class are immutable: each method call involves
 * creation of new instance copied from current one. Newly created instance
 * is modified according to called method and returned. So, <b>don't
 * forget</b> to store returned object.
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
class AfsQuery extends AfsQueryBase
{
    protected $filter = array();  // afs:filter
    protected $page = 1;          // afs:page
    protected $lang = null;       // afs:lang
    protected $sort = array();    // afs:sort
    protected $facetDefault = array(); // afs:facetDefault

    /**
     * @brief Construct new AFS query object.
     * @param $afs_query [in] instance used for initialization (default:
     *        create new empty instance).
     */
    public function __construct(AfsQuery $afs_query = null)
    {
        parent::__construct($afs_query);
        if ($afs_query != null) {
            $this->filter = $afs_query->filter;
            $this->page = $afs_query->page;
            $this->lang = $afs_query->lang;
            $this->sort = $afs_query->sort;
        } else {
            $this->lang = new AfsLanguage(null);
            $this->facetDefault[] = 'replies=1000';
            $this->auto_set_from = false;
        }
    }

    /** @internal
     * @brief Copy current instance.
     * @return New copied instance.
     */
    protected function copy()
    {
        return new AfsQuery($this);
    }

    /** @brief Action to perform when an assignment occurs.
     *
     * Page number is reset. */
    protected function on_assignment()
    {
        $this->reset_page();
    }


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
        $copy->on_assignment();
        $copy->filter[$facet_id] = array($value);
        return $this->auto_set_from ? $copy->set_from(AfsOrigin::FACET) : $copy;
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
        $copy->on_assignment();
        if (empty($copy->filter[$facet_id]))
        {
            $copy->filter[$facet_id] = array();
        }
        $copy->filter[$facet_id][] = $value;
        return $this->auto_set_from ? $copy->set_from(AfsOrigin::FACET) : $copy;
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
        $copy->on_assignment();
        if (! empty($copy->filter[$facet_id]))
        {
            $pos = array_search($value, $copy->filter[$facet_id]);
            unset($copy->filter[$facet_id][$pos]);
            if (empty($copy->filter[$facet_id]))
            {
                unset($copy->filter[$facet_id]);
            }
        }
        return $this->auto_set_from ? $copy->set_from(AfsOrigin::FACET) : $copy;
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
        return $this->auto_set_from ? $copy->set_from(AfsOrigin::PAGER) : $copy;
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
     * @remark Page value is preserved when this method is called.
     *
     * @param $lang [in] New language to filter on. Empty string or null value
     *        resets current language filter.
     * @exception Exception when provided language is invalid.
     */
    public function set_lang($lang)
    {
        $lang = new AfsLanguage($lang);
        $copy = $this->copy();
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

    /** @brief Checks whether sort parameter is set.
     * @param $name [in] check this specific parameter name (default=null:
     *        checks whether at least one sort parameter is set).
     * @return true when sort parameter is set, false otherwise.
     */
    public function has_sort($name=null)
    {
        if (is_null($name)) {
            return ! empty($this->sort);
        } else {
            return array_key_exists($name, $this->sort);
        }
    }
    /** @brief Resets sort order to AFS default sort order.
     */
    public function reset_sort()
    {
        return $this->set_sort(null);
    }
    /** @brief Defines new sort order.
     *
     * Provided sort parameter should be a built-in facet like: @c afs:weight,
     * @c afs:relevance, @c afs:words ... or user defined facet
     *
     * @param $sort_param [in] new sort parameter. When set to emty string or
     *        null, this call to this method is equivalent to call to
     *        @a reset_sort.
     * @param $order [in] order applied to the given parameter. Allowed values
     *        are AfsSortOrder::DESC (default) or AfsSortOrder:ASC.
     *
     * @exception Exception when provided sort parameter does not conform to
     * required syntax.
     */
    public function set_sort($sort_param, $order=AfsSortOrder::DESC)
    {
        return $this->internal_add_sort(null, $sort_param, $order);
    }
    /** @brief Defines additional sort order.
     *
     * Provided sort parameter should be a built-in facet like: @c afs:weight,
     * @c afs:relevance, @c afs:words (see AfsSortBuiltins)... or user defined
     * facet.
     *
     * @param $sort_param [in] new sort parameter. When set to emty string or
     *        null, this call to this method is equivalent to call to
     *        @a reset_sort.
     * @param $order [in] order applied to the given parameter. Allowed values
     *        are AfsSortOrder::DESC (default) or AfsSortOrder:ASC.
     *
     * @exception Exception when provided sort parameter does not conform to
     * required syntax.
     */
    public function add_sort($sort_param, $order=AfsSortOrder::DESC)
    {
        return $this->internal_add_sort($this->sort, $sort_param, $order);
    }
    /** @brief Retrieves sort order.
     * @deprecated This method will be removed soon!
     * @return sort order as string.
     */
    public function get_sort()
    {
        $result = '';
        $sorts = array();
        foreach ($this->sort as $k => $v) {
            $sorts[] = $k . ',' . $v;
        }
        if (! empty($sorts)) {
            $result = implode(';', $sorts);
        }
        return $result;
    }
    /** @brief Retrieves sort order of the specified parameter.
     * @param $name [in] parameter name to check.
     * @return AfsSortOrder::ASC or AfsSortOrder::DESC.
     * @exception OutOfBoundsException when required sort parameter is not
     *            defined.
     */
    public function get_sort_order($name)
    {
        if (array_key_exists($name, $this->sort)) {
            return $this->sort[$name];
        } else {
            throw new OutOfBoundsException('Unknown sort parameter: ' . $name);
        }
    }
    /** @brief Adds new sort parameter or substitutes existing one.
     *
     * @param $current_value [in] current sort order value.
     * @param $sort_param [in] new sort parameter
     * @param $order [in] sort order
     *
     * @return copy of current query.
     */
    private function internal_add_sort($current_value, $sort_param, $order)
    {
        if ($sort_param == '') {
            $sort_param = null;
        }
        if (! is_null($sort_param)) {
            if (strncmp('afs:', $sort_param, 4) == 0) {
                AfsSortBuiltins::check_value($sort_param, 'Invalid sort parameter: ');
            } elseif (1 != preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $sort_param)) {
                throw new Exception('Invalid sort parameter provided: ' . $sort_param);
            }
            AfsSortOrder::check_value($order, 'Invalid sort order provided: ');

            $new_value = $current_value;
            $new_value[$sort_param] = $order;
        } else {
            $new_value = array();
        }

        $copy = $this->copy();
        $copy->on_assignment();
        $copy->sort = $new_value;
        return $copy;
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
            } elseif ($param == 'sort') {
                foreach ($values as $key => $value) {
                    $result = $result->$adder($key, $value);
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


    protected function get_relevant_parameters()
    {
        $params = array('filter', 'sort');
        if ($this->page != 1)
            $params[] = 'page';
        if (! is_null($this->lang->lang))
            $params[] = 'lang';
        return $params;
    }

    protected function get_additional_parameters()
    {
        return array('facetDefault');
    }
    /**  @} */
}


