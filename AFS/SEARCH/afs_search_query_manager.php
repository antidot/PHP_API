<?php
require_once "AFS/SEARCH/afs_facet_manager.php";

/** @brief Manage search query.
 *
 * Manage configured facets and set up adequate query to provide it to AFS
 * search engine. */
class AfsSearchQueryManager
{
    private $connector = null;
    private $config = null;

    /** @brief Construct new search query connector.
     * @param $connector [in] Connector used to submit a query to AFS search engine.
     * @param $config [in] reference to @a AfsHelperConfiguration instance with
     *        appropriate @a AfsFacetManager defined.
     */
    public function __construct(AfsConnectorInterface $connector,
        AfsHelperConfiguration $config)
    {
        $this->connector = $connector;
        $this->config = $config;
    }

    /** @brief Send query to AFS search engine.
     * @param $query [in] @a AfsQuery object to use in order to generate
     *        appropriate AFS query.
     * @return reply of AFS search engine.
     */
    public function send(AfsQuery $query)
    {
        $query->initialize_user_and_session_id($this->config->get_user_session_manager());
        $params = $this->convert_to_param($query);
        $this->add_facet_options($query, $params);
        return $this->connector->send($params);
    }

    /** @internal
     * @brief Add options specific to facets
     *
     * Currently managed options are:
     * - <tt>facet stickyness</tt> to defined global et specific facet stickyness.
     * - <tt>facet order</tt> when using strict strict ordering mode.
     *
     * @param $query [in] Query along with its configuration.
     * @param $params [in-out] array of parameter to update with facet options.
     */
    private function add_facet_options(AfsQuery $query, &$params)
    {
        $facet_mgr = $query->get_facet_manager();
        if ($facet_mgr->get_default_stickyness()) {
            if (! array_key_exists('afs:facetDefault', $params))
                $params['afs:facetDefault'] = array();
            $params['afs:facetDefault'][] = 'sticky=true';
            $default_sticky = true;
        } else {
            $default_sticky = false;
        }

        if ($facet_mgr->has_facets()) {
            if (empty($params['afs:facet']))
                $params['afs:facet'] = array();
            foreach ($facet_mgr->get_facets() as $name => $facet) {
                $sticky = $facet_mgr->is_sticky($facet);
                if ($default_sticky != $sticky)
                    $params['afs:facet'][] = $name . ',sticky=' .  ($sticky ? 'true' : 'false');
            }
        }
        if ($facet_mgr->is_facet_order_strict())
            $params['afs:facetOrder'] = implode(',', array_keys($facet_mgr->get_facets()));
    }

    /** @internal
     * @brief Convert @a AfsQuery to arrays of parameters
     *
     * Array of parameters can be used to query AFS search engine. Each
     * key/value corresponds to parameter name and parameter value or array of
     * parameter values respectively.
     * You can easily get appropriate string with following call:
     * @code
     * $params = qm->convert_to_param($afs_query);
     * $string_params = array();
     * foreach ($params as $k => $vs) {
     *   if (is_array($vs)) {
     *     foreach ($vs as $v) {
     *       $string_params[] = urlencode($k) . '=' . urlencode($v);
     *     }
     *   } else {
     *     $string_params[] = urlencode($k) . '=' . urlencode($vs);
     *   }
     * }
     * $url_params = implode('&', $string_params);
     * @endcode
     *
     *
     * @param $query [in] @a AfsQuery to transform.
     *
     * @return string usable as URL parameter.
     *
     * @exception Exception when filter on unconfigured facet is used.
     */
    private function convert_to_param(AfsQuery $query)
    {
        $params = array();
        foreach ($query->get_parameters() as $param => $values) {
            $param_array = explode('@', $param);
            if (count($param_array) === 2)
                $feed = $param_array[1];
            else
                $feed = null;
            $param_name = $param_array[0];

            if ($param_name =='filter') {
                foreach ($values as $facet => $ids)
                    $this->fill_in_filter($params, $this->format_filter($query, $facet, $ids), $feed);
            } elseif ($param_name == 'sort') {
                if (!empty($values)) {
                    foreach ($values as $name => $order) {
                        $this->fill_in_sort($params, $this->format_sort($name, $order), $feed);
                    }
                }
            } elseif ($param_name == 'advancedFilter') {
                foreach ($values as $value)
                    $this->fill_in_filter($params, $value, $feed);
            } elseif ($param_name == 'nativeFunctionFilter') {
                foreach ($values as $value)
                    $this->fill_in_filter($params, $value, $feed);

            } elseif ($param_name == 'nativeFunctionSort') {
                foreach ($values as $value) {
                    $params['afs:sort'][] = $value;
                }
            } else {
                $this->fill_in_param($params, $param_name, $values, $feed);
            }
        }
        $params = array_merge($params, $query->get_custom_parameters());
        return $params;
    }

    private function fill_in_filter(array& $params, $value, $feed=null)
    {
        if (is_null($feed)) {
            if (!array_key_exists('afs:filter', $params))
                $params['afs:filter'] = array();
            $params['afs:filter'][] = $value;
        } else {
            if (!array_key_exists('afs:filter@' . $feed, $params))
                $params['afs:filter@' . $feed] = array();
            $params['afs:filter@' . $feed][] = $value;
        }
    }

    /** @internal
     * @brief Format filter values.
     *
     * @param $query [in] AfsQuery with its configuration.
     * @param $name [in] facet name. It should have already been configured.
     * @param $values [in] filter values for the given facet @a name.
     *
     * @return correctly formatted filter value.
     *
     * @exception Exception when no facet with @a name has been registered.
     */
    private function format_filter($query, $name, $values)
    {
        return $query->get_facet_manager()->get_or_create_facet($name)->join_values($values);
    }

    private function fill_in_param(array& $params, $param_name, $param_value, $feed=null) {
        if (is_null($feed)) {
            $params['afs:' . $param_name] = $param_value;
        } else {
            $params['afs:' . $param_name . '@' . $feed] = $param_value;
        }
    }

    private function fill_in_sort(array& $params, $value, $feed=null) {
        if (is_null($feed)) {
            $params['afs:sort'][] = $value;
        } else {
            $params['afs:sort' . '@' . $feed][] = $value;
        }
    }

    private function format_sort($name, $order)
    {
            return $name  .  ',' . $order;
    }
}


