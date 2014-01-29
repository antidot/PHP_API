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

    /** @brief Construct new instance from <tt>meta</tt> node.
     * @param $meta [in] meta data node of AFS reply.
     */
    public function __construct($meta)
    {
        $this->feed = $meta->uri;
        $this->total_replies = $meta->totalItems;
        $this->duration = $meta->durationMs;
        $this->producer = $meta->producer;
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
        return array('feed' => $this->feed,
                     'total_replies' => $this->total_replies,
                     'duration' => $this->duration,
                     'producer' => $this->producer);
    }
}


