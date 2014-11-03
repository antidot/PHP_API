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
            if ($param == 'filter') {
                foreach ($values as $facet => $ids)
                    $this->fill_in_filter($params, $this->format_filter($query, $facet, $ids));
            } elseif ($param == 'sort') {
                if (!empty($values)) {
                    foreach ($values as $name => $order) {
                        $params['afs:sort'][] = $this->format_sort($name, $order);
                    }
                }
            } elseif ($param == 'advancedFilter') {
                foreach ($values as $value)
                    $this->fill_in_filter($params, $value);
            } elseif ($param == 'functionalFilter') {
                foreach ($values as $functionName => $functionParams) {
                    if (! array_key_exists('afs:filter', $params))
                        $params['afs:filter'] = array();
                    $params['afs:filter'][] = $functionName.'('.implode(",", $functionParams).')';
                }
            } else {
                $params['afs:' . $param] = $values;
            }
        }
        $params = array_merge($params, $query->get_custom_parameters());
        return $params;
    }

    private function fill_in_filter(array& $params, $value)
    {
        if (! array_key_exists('afs:filter', $params))
            $params['afs:filter'] = array();
        $params['afs:filter'][] = $value;
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

    private function format_sort($name, $order)
    {
        return $name . ',' . $order;
    }
}


