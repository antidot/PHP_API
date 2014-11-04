<?php
require_once 'COMMON/afs_language.php';
require_once 'AFS/afs_query_base.php';
require_once 'AFS/SEARCH/afs_sort_order.php';
require_once 'AFS/SEARCH/afs_sort_builtins.php';
require_once 'AFS/SEARCH/afs_cluster_exception.php';
require_once 'AFS/SEARCH/afs_count.php';
require_once 'AFS/SEARCH/afs_facet_manager.php';
require_once 'AFS/SEARCH/afs_facet_default.php';
require_once 'AFS/SEARCH/afs_fts_mode.php';

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
    protected $facet_mgr = null;

    protected $filter = array();  // afs:filter, filter on facets ids
    protected $nativeFunctionFilter = array(); // use native search engine function such as geo:dist, vfst ...
    protected $page = 1;          // afs:page
    protected $lang = null;       // afs:lang
    protected $sort = array();    // afs:sort
    protected $facetDefault = null; // afs:facetDefault
    protected $cluster = null;
    protected $maxClusters = null;
    protected $overspill = null;
    protected $count = null;      // afs:count for cluster mode
    protected $advancedFilter = array();  // exposed only to AFS search engine
    protected $ftsDefault = null;   // afs:ftsDefault
    protected $clientData = array();   // afs:clientData

    /**
     * @brief Construct new AFS query object.
     * @param $afs_query [in] instance used for initialization (default:
     *        create new empty instance).
     */
    public function __construct(AfsQuery $afs_query = null)
    {
        parent::__construct($afs_query);
        if ($afs_query != null) {
            $this->facet_mgr = $afs_query->facet_mgr->copy();
            $this->filter = $afs_query->filter;
            $this->page = $afs_query->page;
            $this->lang = $afs_query->lang;
            $this->sort = $afs_query->sort;
            $this->facetDefault = $afs_query->facetDefault->copy();
            $this->cluster = $afs_query->cluster;
            $this->maxClusters = $afs_query->maxClusters;
            $this->overspill = $afs_query->overspill;
            $this->count = $afs_query->count;
            $this->advancedFilter = $afs_query->advancedFilter;
            $this->ftsDefault = $afs_query->ftsDefault;
            $this->clientData = $afs_query->clientData;
            $this->nativeFunctionFilter = $afs_query->nativeFunctionFilter;
        } else {
            $this->facet_mgr = new AfsFacetManager();
            $this->lang = new AfsLanguage(null);
            $this->facetDefault = new AfsFacetDefault();
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

    private function set_native_function_filter(AfsFilterWrapper $filter) {
        $copy = $this->copy();
        $copy->nativeFunctionFilter = array($filter->to_string());
        return $copy;
    }

    private function remove_native_function_filter($native_function) {
        $copy = $this->copy();

        // removed existing geo:dist filter
        $removed = false;
        $cpt = 0;
        while (! $removed && $cpt < count($copy->nativeFunctionFilter)) {
            if (substr($copy->nativeFunctionFilter[$cpt], 0, count($native_function)) == $native_function) {
                unset($copy->nativeFunctionFilter[$cpt]);
                $removed = true;
            } else {
                $cpt++;
            }
        }
        return $copy;
    }


    /** @name Filter management
     * @{ */

    /** @brief Assign new value(s) to specific facet replacing any existing one.
     * @param $facet_id [in] id of the facet to update.
     * @param $values [in] new value(s) to filter on.
     * @return new up to date instance.
     */
    public function set_filter($facet_id, $values)
    {
        $copy = $this->copy();
        $copy->on_assignment();
        if (! is_array($values))
            $values = array($values);
        $copy->filter[$facet_id] = $values;
        return $this->auto_set_from ? $copy->set_from(AfsOrigin::FACET) : $copy;
    }

    /** @brief Assign new value(s) to specific facet.
     * @param $facet_id [in] id of the facet for which new @a value should be
     *        added.
     * @param $values [in] value(s) to add to the facet.
     * @return new up to date instance.
     */
    public function add_filter($facet_id, $values)
    {
        $copy = $this->copy();
        $copy->on_assignment();
        if (empty($copy->filter[$facet_id]))
            $copy->filter[$facet_id] = array();
        if (! is_array($values))
            $values = array($values);
        $copy->filter[$facet_id] = array_merge($copy->filter[$facet_id], $values);
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
        if (! empty($copy->filter[$facet_id])) {
            $pos = array_search($value, $copy->filter[$facet_id]);
            unset($copy->filter[$facet_id][$pos]);
            if (empty($copy->filter[$facet_id]))
                unset($copy->filter[$facet_id]);
        }
        return $this->auto_set_from ? $copy->set_from(AfsOrigin::FACET) : $copy;
    }

     /**
      * @brief set a new geolocation filter (replacing existing one) using a center point and a range.
      * @param $lat the center point latitude
      * @param $lon the center point longitude
      * @param int $range the range used to filter
      * @param string $lat_facet_id the facet id used to compare latitudes
      * @param string $lon_facet_id the facet id used to compare longitude
      * @return new up to date instance
      */
    public function set_geoDist_filter($lat, $lon, $range, $lat_facet_id='geo:lat', $lon_facet_id='geo:long') {
        // remove existing geoDist filter
        $copy = $this->remove_native_function_filter(AfsNativeFunction::Geo_dist);

        // create the filter
        $filter = native_function_filter(AfsNativeFunction::Geo_dist, array($lat,$lon,$lat_facet_id,$lon_facet_id));
        // add operator and operand
        $filter = $filter->less->value($range);
        // set the new filter
        return $copy->set_native_function_filter($filter);
    }

    /**
     * @brief remove geolocation filter if exists
     * @return new up to date instance
     */
    public function remove_geoDist_filter() {
       return $this->remove_native_function_filter(AfsNativeFunction::Geo_dist);
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
        if (empty($this->filter[$facet_id])) {
            return false;
        } else {
            if (! isset($value))
                return true;
            else
                return in_array($value, $this->filter[$facet_id]);
        }
    }
    /** @brief Retrieve the list of values for specific facet id.
     * @remark You should ensure that the required @a facet_id is valid.
     * @param $facet_id [in] facet id to consider.
     * @return list of values associated to the given @a facet_id.
     */
    public function get_filter_values($facet_id)
    {
        if (array_key_exists($facet_id, $this->filter)) {
          return $this->filter[$facet_id];
        }
        throw new AfsFilterException("$facet_id doesn't exist");
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

    /** @name Advanced filter management
     *
     * These filters are intended to be exposed to AFS search engine only.
     * @{ */
    /** @brief Checks whether at least one advanced filter is defined.
     * @return @c True when one or more advanced filters have been defined,
     *         @c false otherwise.
     */
    public function has_advanced_filter()
    {
        return ! empty($this->advancedFilter);
    }
    /** @brief Retrieves advanced filters.
     * @return Advanced filters.
     */
    public function get_advanced_filters()
    {
        return $this->advancedFilter;
    }
    /** @brief Defines new advanced filter replacing any existing ones.
     * @param $filter [in] Advanced filter to set.
     * @return new up to date instance.
     */
    public function set_advanced_filter(AfsFilterWrapper $filter)
    {
        $copy = $this->copy();
        $copy->advancedFilter = array($filter->to_string());
        return $copy;
    }
    /** @brief Appends new advanced filter to the query.
     * @param $filter [in] Advanced filter to add.
     * @return new up to date instance.
     */
    public function add_advanced_filter(AfsFilterWrapper $filter)
    {
        $copy = $this->copy();
        $copy->advancedFilter[] = $filter->to_string();
        return $copy;
    }
    /** @brief Remove any advanced filter definition from the query.
     * @return new up to date instance.
     */
    public function reset_advanced_filter()
    {
        $copy = $this->copy();
        $copy->advancedFilter = array();
        return $copy;
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

    /** @name Clustering
     * @{ */

    /** @brief Checks whether cluster is set for current query.
     * @return @c True when cluster is defined for the query, @c false otherwise.
     */
    public function has_cluster()
    {
        return ! is_null($this->cluster);
    }
    /** @brief Defines new cluster query.
     *
     * @param $facet_id [in] Facet identifier used to make clusters.
     * @param $replies_per_cluster [in] Number of replies provided by AFS
     *        search engine per cluster reply.
     * @return new up to date instance.
     */
    public function set_cluster($facet_id, $replies_per_cluster)
    {
        $copy = $this->copy();
        $copy->on_assignment();
        $copy->cluster = $facet_id . ',' . $replies_per_cluster;
        return $copy;
    }
    /** @brief Unsets cluster definition.
     * @return new up to date instance.
     */
    public function unset_cluster()
    {
        $copy = $this->copy();
        $copy->cluster = null;
        $copy->maxClusters = null;
        $copy->overspill = null;
        $copy->count = null;
        return $copy;
    }
    /** @brief Retrieves cluster identifier.
     * @return Filter identifier used to make the clusters.
     * @exception AfsUninitializedClusterException when no cluster has been previously set.
     */
    public function get_cluster_id()
    {
        $tmp = $this->get_splitted_cluster_definition();
        return $tmp[0];
    }
    /** @brief Retrieves maximum number of replies per cluster.
     * @return Number of replies per cluster reply.
     * @exception AfsUninitializedClusterException when no cluster has been previously set.
     */
    public function get_nb_replies_per_cluster()
    {
        $tmp = $this->get_splitted_cluster_definition();
        return $tmp[1];
    }
    /** @internal
     * @brief Split cluster property to retrieve facet id and nb replies per cluster.
     */
    private function get_splitted_cluster_definition()
    {
        $this->check_cluster_initialization();
        return explode(',', $this->cluster);
    }
    /** @brief Checks whether number of cluster is limited.
     *
     * When no limit is set, one cluster is created per facet value.
     * @return @c True when number of clusters is limited, @c false otherwise.
     */
    public function has_max_clusters()
    {
        return ! is_null($this->maxClusters);
    }
    /** @brief Defines maximum number of cluster replies shown in AFS response.
     * @param $max_nb_of_clusters [in] Maximum number of clusters.
     * @return new up to date instance.
     */
    public function set_max_clusters($max_nb_of_clusters)
    {
        $this->check_cluster_initialization();
        $copy = $this->copy();
        $copy->on_assignment();
        $copy->maxClusters = $max_nb_of_clusters;
        return $copy;
    }
    /** @internal
     * Useful for parse_from_parameter method.
     */
    protected function set_maxClusters($max_nb_of_clusters)
    {
        return $this->set_max_clusters($max_nb_of_clusters);
    }
    /** @brief Retrieves maximum number of clusters.
     * @return Maximum number of clusters or @c null when not set (ie no limit).
     */
    public function get_max_clusters()
    {
        return $this->maxClusters;
    }
    /** @brief Checks wether overspill has been activated.
     * @return @c True when overspill is active, @c false otherwise.
     */
    public function has_overspill()
    {
        return ! is_null($this->overspill);
    }
    /** @brief Activates or deactivate overspill mode.
     *
     * When overspill is activated, replies that could not be fitted in
     * clusters are added, in sorted order, after all clusters.
     *
     * @param $status [in] Activates (default: @c true) or deactivates (@c false)
     *        overspill.
     * @return new up to date instance.
     */
    public function set_overspill($status=true)
    {
        $this->check_cluster_initialization();
        $copy = $this->copy();
        if (true == $status)
            $copy->overspill = 'true';
        else
            $copy->overspill = null;
        return $copy;
    }
    /** @brief Retrieves count mode when cluster mode is active.
     *
     * Count mode influences number of replies value which corresponds to the
     * number of documents (default) or the number of clusters.
     *
     * @return Current count mode. AfsCount::DOCUMENTS, AfsCount::CLUSTERS or
     *         @c null when no specific count mode has been set.
     */
    public function get_count_mode()
    {
        return $this->count;
    }
    /** @brief Defines new count mode.
     *
     * @param $count_mode [in] New count mode to set. Available values are
     *        AfsCount::DOCUMENTS, AfsCount::CLUSTERS or null to rely on
     *        default AFS search engine count mode.
     *
     * @return new up to date instance.
     */
    public function set_count($count_mode)
    {
        $this->check_cluster_initialization();
        if (!is_null($count_mode))
            AfsCount::check_value($count_mode, 'Invalid count mode: ');
        $copy = $this->copy();
        $copy->on_assignment();
        $copy->count = $count_mode;
        return $copy;
    }
    /** @internal
     * Checks whether cluster has been initialized otherwise raise an exception.
     */
    private function check_cluster_initialization()
    {
        if (! $this->has_cluster())
            throw new AfsUninitializedClusterException();
    }
    /** @} */

    /** @name Full configuration through array of parameters
     * @{ */

    /** @brief Creates full query from array of parameters.
     *
     * Unknown parameters are silently ignored.
     *
     * @param $params [in] structured array of parameters.
     * @return correctly initialized query.
     */
    public static function create_from_parameters(array $params)
    {
        $result = AfsQuery::work_on_specific_parameters($params);
        $page = $result->get_page(); # page can be reset by some method calls

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
            } elseif (is_object($result) && is_callable(array($result, $adder))) {
                foreach ($values as $value) {
                    $result = $result->$adder($value);
                }
            } elseif (is_object($result) && is_callable(array($result, $setter))) {
                $result = $result->$setter($values);
            } else {
                // Store unknown parameter as a custom one
                $result->set_custom_parameter($param, $values);
            }
        }
        $result->page = $page;
        return $result;
    }

    private static function work_on_specific_parameters(array& $params)
    {
        $result = new AfsQuery();
        if (array_key_exists('cluster', $params)) {
            $result->cluster = $params['cluster'];
            unset($params['cluster']);
        }
        if (array_key_exists('page', $params)) {
            $result->page = $params['page'];
            unset($params['page']);
        }
        return $result;
    }


    protected function get_relevant_parameters()
    {
        $params = array('filter', 'sort', 'cluster', 'maxClusters', 'overspill', 'count', 'ftsDefault', 'clientData');
        if ($this->page != 1)
            $params[] = 'page';
        if (! is_null($this->lang->lang))
            $params[] = 'lang';
        return $params;
    }

    protected function get_additional_parameters()
    {
        return array('facetDefault', 'advancedFilter', 'nativeFunctionFilter');
    }
    /**  @} */

    /** @name Facet management
     *@{ */

    /** @brief Retrieves facet manager.
     * @return Facet manager associated to this instance.
     */
    public function get_facet_manager()
    {
        return $this->facet_mgr;
    }

    /** @brief Defines standard selection mode for all facets.
     *
     * This is the default mode.
     *
     * Standard selection mode allows to filter on one or more facet values
     * whereas only relevant facet values are present in AFS search reply. See
     * AfsFacetMode::AND_MODE for simple example.
     */
    public function set_default_standard_selection_facets()
    {
        $copy = $this->copy();
        $copy->facet_mgr->set_default_facets_mode(AfsFacetMode::AND_MODE);
        return $copy;
    }
    /** @brief Defines multi-selection mode for all facets.
     *
     * Replaces default mode (standard selection facets).
     *
     * Multi-selection mode allows to filter on one or more facet values whereas
     * all facet values are still present in AFS search reply. See
     * AfsFacetMode::OR_MODE for simple example.
     */
    public function set_default_multi_selection_facets()
    {
        $copy = $this->copy();
        $copy->facet_mgr->set_default_facets_mode(AfsFacetMode::OR_MODE);
        return $copy;
    }
    /** @brief Defines mono-selection mode for all facets.
     *
     * Replaces default mode (standard selection facets).
     *
     * Mono-selection mode allows to filter on one facet value whereas all facet
     * values are still present in AFS search reply. Selecting new facet value
     * replaces previously selected one. See AfsFacetMode::OR_MODE for simple
     * example.
     */
    public function set_default_mono_selection_facets()
    {
        $copy = $this->copy();
        $copy->facet_mgr->set_default_facets_mode(AfsFacetMode::SINGLE_MODE);
        return $copy;
    }

    /** @brief Defines facet sort order.
     *
     * @remark Parameters are a list of facet identifiers in the right sort
     *         order (list of strings or array of strings).
     */
    public function set_facet_order()
    {
        $args = get_function_args_as_array(func_get_args());
        $copy = $this->copy();
        $copy->facet_mgr->set_facet_order($args, AfsFacetOrder::STRICT);
        return $copy;
    }

    /** @brief Defines sort order for all facet values.
     *
     * AFS search default sort for facet values is alphanumeric. This method
     * allows to change this behaviour.
     * @remark This configuration is not exposed to AfsQueryCoder.
     *
     * @param $mode [in] Sort mode (see AfsFacetValuesSortMode).
     * @param $order [in] Sort order (see AfsSortOrder).
     *
     * @exception InvalidArgumentException when $mode or $order is invalid.
     */
    public function set_facets_values_sort_order($mode, $order)
    {
        $copy = $this->copy();
        $copy->facetDefault->set_sort_order($mode, $order);
        return $copy;
    }

    /** @brief Defines maximum number of facet values replied per facet.
     *
     * Default maximum value is 1000.
     * @param $nb_replies [in] maximum number of facet values.
     */
    public function set_facets_values_nb_replies($nb_replies)
    {
        $copy = $this->copy();
        $copy->facetDefault->set_nb_replies($nb_replies);
        return $copy;
    }

    /** @brief Defines standard selection mode for one or more facets.
     *
     * See AfsSearch::set_default_standard_selection_facets or
     * AfsFacetMode::AND_MODE for more details.
     *
     * @remark Parameters: one (string) or more facet identifiers (individual
     * strings or array of strings).
     */
    public function set_standard_selection_facets()
    {
        $args = get_function_args_as_array(func_get_args());
        $copy = $this->copy();
        $copy->facet_mgr->set_facets_mode(AfsFacetMode::AND_MODE, $args);
        return $copy;
    }
    /** @brief Defines multi-selection mode for one or more facets.
     *
     * See AfsSearch::set_default_multi_selection_facets or
     * AfsFacetMode::OR_MODE for more details.
     *
     * @remark Parameters: one (string) or more facet identifiers (individual
     * strings or array of strings).
     */
    public function set_multi_selection_facets($ids)
    {
        $args = get_function_args_as_array(func_get_args());
        $copy = $this->copy();
        $copy->facet_mgr->set_facets_mode(AfsFacetMode::OR_MODE, $args);
        return $copy;
    }
    /** @brief Defines mono-selection mode for one or more facets.
     *
     * See AfsSearch::set_default_mono_selection_facets or
     * AfsFacetMode::SINGLE_MODE for more details.
     *
     * @remark Parameters: one (string) or more facet identifiers (individual
     * strings or array of strings).
     */
    public function set_mono_selection_facets($ids)
    {
        $args = get_function_args_as_array(func_get_args());
        $copy = $this->copy();
        $copy->facet_mgr->set_facets_mode(AfsFacetMode::SINGLE_MODE, $args);
        return $copy;
    }
    /** @brief Defines Full Text Search mode
     *
     *
     * @param $ftsDefault Full Text Search mode
     */
    public function set_fts_default($ftsDefault)
    {
        AfsFtsMode::check_value($ftsDefault, "Invalid Full Text Search mode: ");
        $this->ftsDefault = $ftsDefault;
    }
    /** @brief Defines id or array of ids of client data to retrieve
     * This method overwrite any id set before
     * @param $id id or array of ids of client data
     */
    public function set_client_data($id)
    {
        if (is_array($id)) {
            $this->clientData = $id;
        } else {
            $this->clientData = array($id);
        }
    }
    /**
     * @brief Adds id or array of ids of client data to retrieve
     * @param $id id or array of ids of client data
     */
    public function add_client_data($id)
    {
        if (is_array($id)) {
            $this->clientData = array_merge($this->clientData, $id);
        } else {
            array_push($this->clientData, $id);
        }
    }
    /** @} */
}


