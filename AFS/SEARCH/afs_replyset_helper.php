<?php
require_once 'AFS/SEARCH/afs_pager_helper.php';
require_once 'AFS/SEARCH/afs_cluster_helper.php';
require_once 'AFS/SEARCH/afs_facet_helper.php';
require_once 'AFS/SEARCH/afs_query.php';
require_once 'AFS/SEARCH/afs_producer.php';
require_once 'AFS/SEARCH/afs_base_replyset_helper.php';
require_once 'AFS/SEARCH/afs_reply_helper_factory.php';
require_once 'AFS/SEARCH/afs_response_helper.php';
require_once 'AFS/SEARCH/afs_facet_helper_retriever.php';
require_once 'COMMON/afs_tools.php';


/** @brief Helper for replies from one feed.
 *
 * This helper gives access to underlying helpers for metadata, replies, factes
 * and pager.
 */
class AfsReplysetHelper extends AfsBaseReplysetHelper
{
    private $facets = null;
    private $pager = null;
    private $clusters = array();

    /** @brief Construct new replyset helper instance.
     *
     * @param $reply_set [in] one reply from decoded json reply.
     * @param $query [in] query which has produced current reply.
     * @param $config [in] helper configuration object.
     */
    public function __construct($reply_set, AfsQuery $query, AfsHelperConfiguration $config)
    {
        $reply_helper_factory = new AfsReplyHelperFactory($config->get_reply_text_visitor());
        parent::__construct($reply_set, $config, $reply_helper_factory);
        $this->initialize_facet($reply_set, $query, $config);
        $this->initialize_cluster($reply_set, $query, $config);
        $this->initialize_pager($reply_set, $query, $config);
    }

    protected function initialize_facet($reply_set, $query, $config)
    {
        $facets = array();
        if (property_exists($reply_set, 'facets') && property_exists($reply_set->facets, 'facet')) {
            foreach ($reply_set->facets->facet as $facet) {
                $helper = new AfsFacetHelper($facet, $query, $config);
                $facets[$helper->get_id()] = $helper;
            }
        }
        $facet_mgr = $config->get_facet_manager();
        if ($facet_mgr->has_facets())
            sort_array_by_key(array_keys($facet_mgr->get_facets()), $facets);
        $this->facets = array_values($facets); // preserve compatibility
    }

    protected function initialize_cluster($reply_set, $query, $config)
    {
        if (property_exists($reply_set, 'content') && property_exists($reply_set->content, 'cluster')) {
            $clustering_id = $this->get_meta()->get_cluster_id();
            $facet_helper = AfsFacetHelperRetriever::get_helper($clustering_id, $this->facets);
            $this->update_meta($facet_helper);
            foreach ($reply_set->content->cluster as $cluster) {
                $helper = new AfsClusterHelper($cluster, $this->get_meta(), $facet_helper, $query, $config);
                $this->clusters[$helper->get_id()] = $helper;
            }
        }
    }

    protected function update_meta($facet_helper)
    {
        if (! is_null($facet_helper))
            $this->get_meta()->set_cluster_label($facet_helper->get_label());
    }

    protected function initialize_pager($reply_set, $query, $config)
    {
        if (property_exists($reply_set, 'pager'))
            $this->pager = new AfsPagerHelper($reply_set->pager, $this->meta, $query, $config);
    }


    /** @brief Check whether facets are defined.
     * @return @c True when at least one facet is defined, @c false otherwise.
     */
    public function has_facet()
    {
        return ! empty($this->facets);
    }
    /** @brief List of facets.
     * @return facets.
     */
    public function get_facets()
    {
        return $this->facets;
    }

    /** @brief Check whether clusters are defined.
     * @return @c True when at least one cluster is defined, @c false otherwise.
     */
    public function has_cluster()
    {
        return ! empty($this->clusters);
    }
    /** @brief Retrieves all cluster replies.
     * @return cluster replies.
     */
    public function get_clusters()
    {
        return $this->clusters;
    }
    /** @brief Retrieves replies from all clusters.
     *
     * Replies from all cluster are merged preserving cluster result order.
     * @return Merged replies from all clusters.
     */
    public function get_cluster_replies()
    {
        $replies = array();
        foreach ($this->clusters as $cluster)
            $replies = array_merge($replies, $cluster->get_replies());
        return $replies;
    }
    /** @brief Retrieves replies from all clusters and overspill replies.
     *
     * Replies from all clusters are merged with overspill replies. This is a
     * shortcut for a call of both following methods: AfsReplysetHelper::get_cluster_replies
     * and AfsReplysetHelper::get_replies.
     *
     * @return merged replies.
     */
    public function get_all_replies()
    {
        return array_merge($this->get_cluster_replies(), $this->get_replies());
    }

    /** @brief Checks whether pager is defined.
     * @return @c True when pager exists, @c false otherwise.
     */
    public function has_pager()
    {
        if (is_null($this->pager))
            return false;
        else
            return true;
    }
    /** @brief Retrieves pager object.
     * @return instance of @a AfsPagerHelper.
     */
    public function get_pager()
    {
        return $this->pager;
    }

    /** @brief Retrieve replyset as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c meta: array of meta data (@a AfsMetaHelper::format),
     * @li @c nb_replies: number of replies on the current page.
     * @li @c replies: standard or Promote reply.
     * @li @c facets: array of facets (@a AfsFacetHelper::format),
     * @li @c pager: array of pages (@a AfsPagerHelper::format),
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        $result = parent::format();
        if ($this->has_facet()) {
            $result['facets'] = array();
            foreach ($this->get_facets() as $facet_id => $facet) {
                $result['facets'][$facet_id] = $facet->format();
            }
        }
        if ($this->has_cluster()) {
            $formatted_cluster = array();
            foreach ($this->get_clusters() as $cluster_id => $cluster)
                $formatted_cluster[$cluster_id] = $cluster->format();
            $result['clusters'] = $formatted_cluster;
        }
        if ($this->has_pager())
            $result['pager'] = $this->get_pager()->format();
        return $result;
    }
}


