<?php
require_once "afs_meta_helper.php";
require_once "afs_reply_helper.php";
require_once "afs_pager_helper.php";
require_once "afs_facet_helper.php";
require_once "afs_helper_base.php";
require_once "afs_producer.php";


/** @brief Helper for replies from one feed.
 *
 * This helper gives access to underlying helpers for metadata, replies, factes 
 * and pager.
 */
class AfsReplysetHelper extends AfsHelperBase
{
    private $meta = null;
    private $facets = array();
    private $replies = array();
    private $pager = null;

    /** @brief Construct new replyset helper instance.
     *
     * @param $reply_set [in] one reply from decoded json reply.
     * @param $facet_mgr [in] @a AfsFacetManager used to create appropriate
     *        queries.
     * @param $query [in] query which has produced current reply.
     * @param $coder [in] @a AfsQueryCoderInterface if set it will be used to
     *        create links (default: null).
     * @param $format [in] if set to AFS_ARRAY_FORMAT (default), all underlying
     *        helpers will be formatted as array of data, otherwise they are
     *        kept as is.
     * @param $visitor [in] text visitor implementing @a AfsTextVisitorInterface
     *        used to extract title and abstract contents. If not set, default
     *        visitor is used (see @a AfsReplyHelper).
     */
    public function __construct($reply_set, AfsFacetManager $facet_mgr,
        AfsQuery $query, AfsQueryCoderInterface $coder=null,
        $format=AFS_ARRAY_FORMAT, AfsTextVisitorInterface $visitor=null)
    {
        $this->check_format($format);
        $meta_helper = new AfsMetaHelper($reply_set->meta);
        $this->meta = $format == AFS_ARRAY_FORMAT ? $meta_helper->format() : $meta_helper;
        if (property_exists($reply_set, 'content') && property_exists($reply_set->content, 'reply')) {
            foreach ($reply_set->content->reply as $reply) {
                $reply_helper = new AfsReplyHelper($reply, $visitor);
                $this->replies[] = $format == AFS_ARRAY_FORMAT ? $reply_helper->format() : $reply_helper;
            }
        }
        if (property_exists($reply_set, 'facets') && property_exists($reply_set->facets, 'facet')) {
            foreach ($reply_set->facets->facet as $facet) {
                $facet_helper = new AfsFacetHelper($facet, $facet_mgr, $query, $coder, $format);
                $this->facets[] = $format == AFS_ARRAY_FORMAT ? $facet_helper->format() : $facet_helper;
            }
        }
        if (property_exists($reply_set, 'pager')) {
            $pager_helper = new AfsPagerHelper($reply_set->pager, $query, $coder);
            $this->pager = $format == AFS_ARRAY_FORMAT ? $pager_helper->format() : $pager_helper;
        }
    }

    /** @brief Retrieve meta data object.
     * @return instance of @a AfsMetaHelper.
     */
    public function get_meta()
    {
        return $this->meta;
    }
    /** @brief Check whether facets are defined.
     * @return true when at least one facet is defined, false otherwise.
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
    /** @brief Retrieve all replies of current page.
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
    /** @brief Retrieve pager object.
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
     * @li @c facets: array of facets (@a AfsFacetHelper::format),
     * @li @c pager: array of pages (@a AfsPagerHelper::format),
     * @li @c nb_replies: number of replies on the current page.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        return array('meta' => $this->get_meta(),
                     'facets' => $this->get_facets(),
                     'nb_replies' => $this->get_nb_replies(),
                     'replies' => $this->get_replies(),
                     'pager' => $this->get_pager());
    }
}

?>
