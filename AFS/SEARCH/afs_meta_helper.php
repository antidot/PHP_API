<?php
require_once "COMMON/afs_helper_base.php";

/** @brief AFS reply meta data.
 *
 * Wrapper to access main meta data.
 */
class AfsMetaHelper extends AfsHelperBase
{
    private $feed = null;
    private $total_replies = null;
    private $duration = null;
    private $producer = null;
    private $cluster = null;
    private $cluster_label = null;

    /** @brief Construct new instance from <tt>meta</tt> node.
     * @param $meta [in] meta data node of AFS reply.
     */
    public function __construct($meta)
    {
        $this->feed = $meta->uri;
        $this->total_replies = $meta->totalItems;
        $this->duration = $meta->durationMs;
        $this->producer = $meta->producer;
        if (property_exists($meta, 'cluster')) {
            $this->cluster = $meta->cluster;
            $this->cluster_label = $meta->cluster;
        }
    }

    /** @brief Retrieve feed name of the reply.
     * @return feed name.
     */
    public function get_feed()
    {
        return $this->feed;
    }
    /** @brief Retrieve total number of replies for current feed.
     * @return number of replies for the feed.
     */
    public function get_total_replies()
    {
        return $this->total_replies;
    }
    /** @brief Retrieve reply computation time for the feed.
     * @return duration in milliseconds.
     */
    public function get_duration()
    {
        return $this->duration;
    }
    /** @brief Retrieve producer type.
     * @return producer.
     */
    public function get_producer()
    {
        return $this->producer;
    }

    /** @brief Checks whether response contains clusters.
     * @return @c True in cluster mode, @c false otherwise.
     */
    public function has_cluster()
    {
        return (! is_null($this->cluster));
    }

    /** @brief Retrieves filter identifier used to clusterize.
     * @return filter identifier.
     */
    public function get_cluster_id()
    {
        return $this->cluster;
    }

    /** @brief Retrieves filter label used to clusterize.
     * @return filter label.
     */
    public function get_cluster_label()
    {
        return $this->cluster_label;
    }

    /** @internal
     * @brief Set cluster label (internal use only).
     */
    public function set_cluster_label($label)
    {
        $this->cluster_label = $label;
    }

    /** @brief Retrieve meta as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c feed: feed name,
     * @li @c total_replies: total number of replies for current query,
     * @li @c duration: time to compute the reply (in milliseconds),
     * @li @c producer: name of the producer agent.
     *
     * @return array filled with key/value pairs.
     */
    public function format()
    {
        $result = array('feed' => $this->feed,
                        'total_replies' => $this->total_replies,
                        'duration' => $this->duration,
                        'producer' => $this->producer);
        if (! is_null($this->cluster)) {
            $result['cluster'] = $this->cluster;
            $result['cluster_label'] = $this->cluster_label;
        }
        return $result;
    }
}


