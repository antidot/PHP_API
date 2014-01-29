<?php
require_once 'AFS/SEARCH/afs_pager_helper.php';
require_once 'AFS/SEARCH/afs_facet_helper.php';
require_once 'AFS/SEARCH/afs_query.php';
require_once 'AFS/SEARCH/afs_producer.php';
require_once 'AFS/SEARCH/afs_base_replyset_helper.php';
require_once 'AFS/SEARCH/afs_reply_helper_factory.php';
require_once 'AFS/SEARCH/afs_response_helper.php';


/** @brief Helper for replies from one feed.
 *
 * This helper gives access to underlying helpers for metadata, replies, factes
 * and pager.
 */
class AfsReplysetHelper extends AfsBaseReplysetHelper
{
    private $facets = array();
    private $pager = null;

    /** @brief Construct new replyset helper instance.
     *
     * @param $reply_set [in] one reply from decoded json reply.
     * @param $facet_mgr [in] @a AfsFacetManager used to create appropriate
     *        queries.
     * @param $query [in] query which has produced current reply.
     * @param $config [in] helper configuration object.
     */
    public function __construct($reply_set, AfsQuery $query, AfsHelperConfiguration $config)
    {
        parent::__construct($reply_set, $config, new AfsReplyHelperFactory($config->get_reply_text_visitor()));
        $this->initialize_facet($reply_set, $query, $config);
        $this->initialize_pager($reply_set, $query, $config);
    }

    protected function initialize_facet($reply_set, $query, $config)
    {
        if (property_exists($reply_set, 'facets') && property_exists($reply_set->facets, 'facet')) {
            foreach ($reply_set->facets->facet as $facet) {
                $facet_helper = new AfsFacetHelper($facet, $query, $config);
                $this->facets[] = $config->is_array_format() ? $facet_helper->format() : $facet_helper;
            }
        }
    }

    protected function initialize_pager($reply_set, $query, $config)
    {
        if (property_exists($reply_set, 'pager')) {
            $pager_helper = new AfsPagerHelper($reply_set->pager, $query, $config);
            $this->pager = $config->is_array_format() ? $pager_helper->format() : $pager_helper;
        }
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
        $result['facets'] = $this->get_facets();
        $result['pager'] = $this->get_pager();
        return $result;
    }
}


