<?php
require_once 'COMMON/afs_service.php';
require_once 'AFS/SEARCH/afs_search_connector.php';
require_once 'AFS/SEARCH/afs_helper_configuration.php';
require_once 'AFS/SEARCH/afs_query_coder.php';
require_once 'AFS/SEARCH/afs_facet_manager.php';
require_once 'AFS/SEARCH/afs_search_query_manager.php';
require_once 'AFS/SEARCH/afs_response_helper.php';
require_once 'AFS/SEARCH/afs_query.php';

/** @brief Facade for AFS search engine query. */
class AfsSearch
{
    private $service = null;
    private $connector = null;
    private $config = null;
    private $query = null;

    /** @brief Constructs AFS search facade.
     *
     * @param $host [in] server hosting the required service.
     * @param $id [in] identifier of the desired service.
     * @param $status [in] status of the desired service (see @ref AfsServiceStatus).
     */
    public function __construct($host, $id, $status=AfsServiceStatus::STABLE)
    {
        $this->service = new AfsService($id, $status);
        $this->connector = new AfsSearchConnector($host, $this->service);
        $this->config = new AfsHelperConfiguration();
        $this->query = new AfsQuery();
    }

    /** @name Query coder
     *
     * This coder is useful only when you want AFS helpers to generate
     * appropriate links.
     * @{ */

    /** @brief Defines new query coder.
     * @param $query_coder [in] query coder used to encode query into URL format
     *        and decode query from URL parameters.
     */
    public function set_query_coder(AfsQueryCoderInterface $query_coder)
    {
        $this->config->set_query_coder($query_coder);
    }
    /** @brief Builds query using URL parameters.
     *
     * Use defined query coder or instanciate default query coder when none has
     * yet been defined.
     *
     * @return the built query.
     */
    public function build_query_from_url_parameters()
    {
        if (! $this->config->has_query_coder()) {
            $this->config->set_query_coder(new AfsQueryCoder());
        }
        $this->query = $this->config->get_query_coder()->build_query($_GET);
        return $this->query;
    }
    /** @} */

    /** @name Global facet configuration
     * @{ */

    /** @brief Defines multi-selection mode for all facets.
     *
     * This is the default mode.
     *
     * Multi-selection mode allows to filter on one or more facet values whereas
     * all facet values are still present in AFS search reply. See
     * AfsFacetMode::OR_MODE for simple example.
     */
    public function set_default_multi_selection_facets()
    {
        $this->config->get_facet_manager()->set_default_facets_mode(AfsFacetMode::OR_MODE);
    }
    /** @brief Defines mono-selection mode for all facets.
     *
     * Replaces default mode (multi-selection facets).
     *
     * Mono-selection mode allows to filter on one facet value whereas all facet
     * values are still present in AFS search reply. Selecting new facet value
     * replaces previously selected one. See AfsFacetMode::OR_MODE for simple
     * example.
     */
    public function set_default_mono_selection_facets()
    {
        $this->config->get_facet_manager()->set_default_facets_mode(AfsFacetMode::SINGLE_MODE);
    }
    /** @} */

    /** @name Specific facet configuration
     * @{ */

    /** @brief Defines facet sort order.
     * @param $ids [in] List of facet identifiers in the right sort order.
     * @param $mode [in] Sort order mode (see AfsFacetSort for more details).
     */
    public function set_facet_sort_order(array $ids, $mode)
    {
        $this->config->get_facet_manager()->set_facet_sort_order($ids, $mode);
    }
    /** @brief Defines multi-selection mode for one or more facets.
     *
     * See AfsSearch::set_default_multi_selection_facets or
     * AfsFacetMode::OR_MODE for more details.
     *
     * @param $ids [in] One (string) or more facet identifiers (array of strings).
     */
    public function set_multi_selection_facets($ids)
    {
        $this->config->get_facet_manager()->set_facets_mode(AfsFacetMode::OR_MODE, $ids);
    }
    /** @brief Defines mono-selection mode for one or more facets.
     *
     * See AfsSearch::set_default_mono_selection_facets or
     * AfsFacetMode::SINGLE_MODE for more details.
     *
     * @param $ids [in] One (string) or more facet identifiers (array of strings).
     */
    public function set_mono_selection_facets($ids)
    {
        $this->config->get_facet_manager()->set_facets_mode(AfsFacetMode::SINGLE_MODE, $ids);
    }
    /** @brief Configures specific facet.
     * @param $facet [in] New facet to configure.
     */
    public function add_facet(AfsFacet $facet)
    {
        $this->config->get_facet_manager()->add_facet($facet);
    }
    /** @} */

    /** @name Query management
     *
     * Remember that AfsQuery objects are immutable.
     * @{ */

    /** @brief Retrieves current query.
     * @return AFS search query.
     */
    public function get_query()
    {
        return $this->query;
    }
    /** @brief Defines new query.
     * @param $query [in] New query to set.
     */
    public function set_query(AfsQuery $query)
    {
        $this->query = $query;
    }

    /** @brief Executes query.
     * @param $format [in] prefered result format.
     * @return Helper or array depending on chosen $format.
     */
    public function execute($format=AfsHelperFormat::ARRAYS)
    {
        $this->config->set_helper_format($format);
        $query_mgr = new AfsSearchQueryManager($this->connector, $this->config);
        $reply = $query_mgr->send($this->query);
        $helper = new AfsResponseHelper($reply, $this->query, $this->config);
        if (AfsHelperFormat::ARRAYS == $format) {
            return $helper->format();
        } else {
            return $helper;
        }
    }

    /** @brief Retrieves URL used to query AFS search engine.
     *
     * Useful for debug purpose only. It should be called after
     * AfsSearch::execute has been called.
     * @return generated URL for AFS search engine.
     */
    public function get_generated_url()
    {
        return $this->connector->get_generated_url();
    }
    /** @} */

    /** @name Miscellaneous accessors
     * @{ */

    /** @brief Retrieves current AFS service.
     * @return AFS service.
     */
    public function get_service()
    {
        return $this->service;
    }

    /** @brief Retrieves helper configuration.
     * @return AFS Helper configuration.
     */
    public function get_helpers_configuration()
    {
        return $this->config;
    }

    /** @brief Retrieves search engine connector.
     * @return AFS search connector.
     */
    public function get_connector()
    {
        return $this->connector;
    }
    /** @} */
}


