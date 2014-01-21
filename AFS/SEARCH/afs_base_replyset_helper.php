<?php
require_once "AFS/SEARCH/afs_meta_helper.php";
require_once "AFS/SEARCH/afs_producer.php";
require_once "COMMON/afs_helper_base.php";
require_once "COMMON/afs_helper_format.php";

class AfsBaseReplysetHelper extends AfsHelperBase
{
    protected $meta = null;
    protected $replies = array();

    /** @brief Construct new replyset helper instance.
     *
     * @param $reply_set [in] one reply from decoded json reply.
     * @param $format [in] if set to AfsHelperFormat::ARRAYS, all underlying
     *        helpers will be formatted as array of data, otherwise they are
     *        kept as is.
     * @param $factory [in] used to create appropriate reply helper.
     */
    public function __construct($reply_set, $format, AfsReplyHelperFactory $factory)
    {
        $this->check_format($format);
        $this->initialize_meta($reply_set, $format);
        $this->initialize_content($reply_set, $format, $factory);
    }

    protected function initialize_meta($reply_set, $format)
    {
        $meta_helper = new AfsMetaHelper($reply_set->meta);
        $this->meta = $format == AfsHelperFormat::ARRAYS ? $meta_helper->format() : $meta_helper;
    }

    protected function initialize_content($reply_set, $format, $factory)
    {
        if (property_exists($reply_set, 'content') && property_exists($reply_set->content, 'reply')) {
            // Remove this horrible thing!
            $feed = $format == AfsHelperFormat::ARRAYS ? $this->meta['feed'] : $this->meta->get_feed();
            foreach ($reply_set->content->reply as $reply) {
                $reply_helper = $factory->create($feed, $reply);
                $this->replies[] = $format == AfsHelperFormat::ARRAYS ? $reply_helper->format() : $reply_helper;
            }
        }
    }

    /** @brief Retrieves meta data object.
     * @return instance of @a AfsMetaHelper.
     */
    public function get_meta()
    {
        return $this->meta;
    }

    /** @brief Checks whether reply set contains at least one reply.
     * @return true when one or more reply is defined, false otherwise.
     */
    public function has_reply()
    {
        return ! empty($this->replies);
    }
    /** @brief Retrieve number of replies for current page.
     *
     * you can retrieve total number of replies through
     * <tt>get_meta()->get_total_items()</tt>.
     *
     * @return number of replies for current page.
     */
    public function get_nb_replies()
    {
        return count($this->replies);
    }
    /** @brief Retrieves all replies of current page.
     *
     * You can loop on each reply:
     * @code
     * foreach ($replies->get_replies() as $reply) {
     *   // Work on reply
     * }
     * @endcode
     *
     * @return All replies of current page.
     */
    public function get_replies()
    {
        return $this->replies;
    }

    /** @brief Retrieves replyset as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c meta: array of meta data (@a AfsMetaHelper::format),
     * @li @c nb_replies: number of replies on the current page.
     * @li @c replies: standard or Promote reply.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        return array('meta' => $this->get_meta(),
                     'nb_replies' => $this->get_nb_replies(),
                     'replies' => $this->get_replies());
    }
}

?>
