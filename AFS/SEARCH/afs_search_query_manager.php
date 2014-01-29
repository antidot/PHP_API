<?php
require_once "AFS/SEARCH/afs_facet_manager.php";

/** @brief Manage search query.
 *
 * Manage configured facets and set up adequate query to provide it to AFS
 * search engine. */
class AfsSearchQueryManager
{
    private $connector = null;
    private $facet_mgr = null;

    /** @brief Construct new search query connector.
     * @param $connector [in] Connector used to submit a query to AFS search engine.
     * @param $facet_mgr [in] reference to @a AfsFacetManager instance with
     *        appropriate @a AfsFacet defined.
     */
    public function __construct(AfsConnectorInterface $connector,
        AfsFacetManager $facet_mgr)
    {
        $this->connector = $connector;
        $this->facet_mgr = $facet_mgr;
    }

    /** @brief Send query to AFS search engine.
     * @param $query [in] @a AfsQuery object to use in order to generate
     *        appropriate AFS query.
     * @return reply of AFS search engine.
     */
    public function send(AfsQuery $query)
    {
        $params = $this->convert_to_param($query);
        $this->add_facet_options($params);
        return $this->connector->send($params);
    }

    /** @internal
     * @brief Add options specific to facets
     *
     * Currently managed options are:
     * - <tt>afs:facet stickyness</tt> to defined dynamically sticky facets.
     *
     * @param $params [in-out] array of parameter to update with facet options.
     */
    private function add_facet_options(&$params)
    {
        foreach ($this->facet_mgr->get_facets() as $name => $facet) {
            if ($facet->is_sticky()) {
                if (empty($params['afs:facet'])) {
                    $params['afs:facet'] = array();
                }
                $params['afs:facet'][] = $name . ',sticky=true';
            }
        }
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
                if (! empty($values)) {
                    $params['afs:filter'] = array();
                    foreach ($values as $facet => $ids) {
                        $params['afs:filter'][] = $this->format_filter($facet,
                                                                       $ids);
                    }
                }
            } else {
                $params['afs:' . $param] = $values;
            }
        }
        return $params;
    }

    /** @internal
     * @brief Format filter values.
     *
     * This allows to add necessary surrounding double-quotes for facet values
     * which require them.
     *
     * @param $name [in] facet name. It should have already been configured.
     * @param $values [in] filter values for the given facet @a name.
     *
     * @return correctly formatted filter value.
     *
     * @exception Exception when no facet with @a name has been registered.
     */
    private function format_filter($name, $values)
    {
        $facets = $this->facet_mgr->get_facets();
        if (empty($facets[$name])) {
            if (count($values) > 1) {
                error_log('No facet named "' . $name . '" is currently registered. Only first facet value will be used to query AFS search engine');
            }
            return $name . '=' . $values[0];
        } else {
            return $facets[$name]->join_values($values);
        }
    }
}


