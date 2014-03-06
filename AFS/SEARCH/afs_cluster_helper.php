<?php
require_once 'AFS/SEARCH/afs_reply_helper.php';
require_once 'AFS/SEARCH/afs_reply_helper_factory.php';

/** @brief Helper for one cluster reply.
 */
class AfsClusterHelper extends AfsHelperBase
{
    private $id = null;
    private $label = null;
    private $total_replies = null;
    private $replies = array();
    private $query = null;
    private $config = null;

    /** @brief Constructs new cluster helper instance.
     *
     * @param $cluster [in] One cluster reply.
     * @param $meta [in] Reply set meta data.
     * @param $facet_helper [in] Facet helper (used to retrieve value label).
     * @param $query [in] AfsQuery object previously initialized. It is used to
     *        generate new query to filter on this cluster reply.
     * @param $config [in] Helper configuration.
     */
    public function __construct($cluster, AfsMetaHelper $meta, $facet_helper,
        AfsQuery $query, AfsHelperConfiguration $config)
    {
        $formatter = AfsFacetHelperRetriever::get_formatter($meta->get_cluster_id(), $config);
        $this->id = $formatter->format($cluster->id);
        $this->initialize_label($facet_helper);
        $this->total_replies = $cluster->totalItems;
        $factory = new AfsReplyHelperFactory($config->get_reply_text_visitor());
        $this->replies = $factory->create_replies($meta->get_feed(), $cluster);
        $this->query = $query->auto_set_from()->unset_cluster()->add_filter($meta->get_cluster_id(), $this->id);
        $this->config = $config;
    }

    private function initialize_label($facet_helper)
    {
        if (! is_null($facet_helper)) {
            foreach ($facet_helper->get_elements() as $element) {
                if ($this->id == $element->key) {
                    $this->label = $element->label;
                    break;
                }
            }
        }
        if (is_null($this->label))
            $this->label = $this->id;
    }

    /** @brief Retrieves identifier of the facet value defined for current cluster.
     * @return facet value identifier.
     */
    public function get_id()
    {
        return $this->id;
    }

    /** @brief Retrieves label of the facet value defined for current cluster.
     * @return facet value label.
     */
    public function get_label()
    {
        return $this->label;
    }

    /** @brief Retrieves number of replies in the cluster.
     * @return number of replies.
     */
    public function get_total_replies()
    {
        return $this->total_replies;
    }

    /** @brief Checks whether current cluster has replies.
     * @return @c True when at least one reply is present, @c false otherwise.
     */
    public function has_reply()
    {
        return !empty($this->replies);
    }

    /** @brief Retrieves number of replies in current cluster.
     * @return replies in current cluster.
     */
    public function get_nb_replies()
    {
        return count($this->replies);
    }

    /** @brief Retrieves all clustered replies.
     * @return clustered replies.
     */
    public function get_replies()
    {
        return $this->replies;
    }

    /** @brief Retrieves query to filter on current cluster.
     *
     * Using this query allows to
     * @return 
     */
    public function get_query()
    {
        return $this->query;
    }

    /** @brief Retrieves cluster as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c id: cluster id (ie identifier of facet value used to make current cluster),
     * @li @c label: cluster label (ie label of the facet value, fallback to id when no label is available),
     * @li @c total_replies: number of replies in current cluster,
     * @li @c replies: list of replies for current cluster (see AfsReplyHelper::format),
     * @li @c link: link which can be used to filter on this specific cluster.
     *     You MUST have provided query coder to helper configuration otherwise
     *     link is set to empty string.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        $formatted_replies = array();
        foreach ($this->replies as $reply)
            $formatted_replies[] = $reply->format();
        if ($this->config->has_query_coder())
            $link = $this->config->get_query_coder()->generate_link($this->query);
        else
            $link = '';

        return array('id' => $this->id,
                     'label' => $this->label,
                     'total_replies' => $this->total_replies,
                     'replies' => $formatted_replies,
                     'link' => $link);
    }
}
