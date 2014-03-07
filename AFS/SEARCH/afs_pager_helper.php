<?php
require_once 'COMMON/afs_helper_base.php';
require_once 'AFS/SEARCH/afs_helper_configuration.php';

/** @brief Helper for pager.
 *
 * This class allows to manage:
 * - <tt>previous</tt> page if it exists.
 * - <tt>next</tt> page if it exists.
 * - <tt>numbered</tt> pages.
 */
class AfsPagerHelper extends AfsHelperBase
{
    private $pager = null;
    private $last_page = null;
    private $query = null;
    private $config = null;
    const PREVIOUS_NAME = 'previousPage';
    const NEXT_NAME = 'nextPage';
    const CURRENT_NAME = 'currentPage';
    const LAST_PAGE = 'lastPage';

    /** @brief Construct helper with pager and current query.
     *
     * @param $pager [in] pager retrieved from reply.
     * @param $meta [in] meta data of the replyset.
     * @param $query [in] current @a AfsQuery which will be used to generate
     *        appropriate queries (see bellow @a get_pages, @a get_previous and
     *        @a get_next).
     * @param $config [in] helper ocnfiguration object.
     *
     * @exception InvalidArgumentException @a pager is invalid.
     */
    public function __construct($pager, AfsMetaHelper $meta, AfsQuery $query,
        AfsHelperConfiguration $config)
    {
        if (! property_exists($pager, AfsPagerHelper::CURRENT_NAME)) {
            throw new InvalidArgumentException('Pager is of the wrong type.');
        }
        $this->pager = $pager;
        $this->query = $query;
        $this->config = $config;
        $this->initialize_last_page($meta);
    }

    /** @brief Retrieves all numbered pages.
     *
     * List all pages in ascending order. A query or a URL is associated with
     * each page depending whether no coder or valid one has been provided.
     *
     * @return array of page => query or URL.
     */
    public function get_pages()
    {
        $result = array();
        foreach ($this->pager->page as $page) {
            $query = $this->query->set_page($page);
            $result[$page] = $this->config->has_query_coder()
                ? $this->config->get_query_coder()->generate_link($query)
                : $query;
        }
        return $result;
    }

    /** @brief Retrieves current page number.
     * @return Current page number.
     */
    public function get_current_no()
    {
        return $this->pager->currentPage;
    }

    /** @brief Checks whether previous page is present in the pager.
     * @return @c True when previous page exists, @c false otherwise.
     */
    public function has_previous()
    {
        if (property_exists($this->pager, AfsPagerHelper::PREVIOUS_NAME)) {
            return true;
        } else {
            return false;
        }
    }
    /** @brief Retrieves query for previous page.
     * @return query for previous page.
     * @exception OutOfBoundsException when there is no previous page.
     */
    public function get_previous()
    {
        return $this->get_typed(AfsPagerHelper::PREVIOUS_NAME);
    }

    /** @brief Checks whether next page is present in the pager.
     * @return @c True when next page exists, @c false otherwise.
     */
    public function has_next()
    {
        if (property_exists($this->pager, AfsPagerHelper::NEXT_NAME)) {
            return true;
        } else {
            return false;
        }
    }
    /** @brief Retrieves query for next page.
     * @return query for next page.
     * @exception OutOfBoundsException when there is no next page.
     */
    public function get_next()
    {
        return $this->get_typed(AfsPagerHelper::NEXT_NAME);
    }

    private function initialize_last_page($meta)
    {
        $page_no = ceil($meta->get_total_replies() / $meta->get_replies_per_page());
        $query = $this->query->set_page($page_no);
        if ($this->config->has_query_coder())
            $second = $this->config->get_query_coder()->generate_link($query);
        else
            $second = $query;
        $this->last_page = array($page_no, $second);
    }
    /** @brief Retrieves last page number along with corresponding query/URL.
     *
     * When a query coder is available, an URL is returned as second paramter
     * instead of AfsQuery.
     *
     * @return Array with last page available and query/URL.
     */
    public function get_last_page()
    {
        return $this->last_page;
    }
    /** @brief Retrieves last page number.
     * @return Last page number.
     */
    public function get_last_page_no()
    {
        return $this->last_page[0];
    }

    /** @brief Retrieves pages as a simple array with key/value pairs.
     *
     * This include @c previous and @c next pages if they are present in AFS
     * search engine reply.
     *
     * All data are stored in <tt>key => value</tt> format:
     * <ul>
     *   <li><tt>previous</tt>: (if present) query or URL to previous page,</li>
     *   <li><tt>&lt;page number></tt>: query or URL for each page number,</li>
     *   <li><tt>next</tt>: (if present) query or URL to next page,</li>
     * </ul>
     *
     * @return 
     */
    public function get_all_pages()
    {
        $pages = array();
        if ($this->has_previous()) {
            $pages['previous'] = $this->get_previous();
        }
        $pages += $this->get_pages();
        if ($this->has_next()) {
            $pages['next'] = $this->get_next();
        }
        return $pages;
    }

    /** @brief Retrieves pages as array.
     *
     * All data are stored in <tt>key => value</tt> format:
     * <ul>
     *   <li><tt>pages</tt>: list of pages (see AfsPagerHelper::get_pages 
     *       for details on the format)</li>
     *   <li><tt>current</tt>: current page number.</li>
     * </ul>
     *
     * Query is returned for each page when no query coder has been provided,
     * otherwise query coder is used to produce appropriate URL.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        return array('pages' => $this->get_all_pages(),
                     'current' => $this->get_current_no());
    }

    private function get_typed($type)
    {
        if (property_exists($this->pager, $type)) {
            $query = $this->query->set_page($this->pager->$type);
            return $this->config->has_query_coder()
                ? $this->config->get_query_coder()->generate_link($query) : $query;
        } else {
            throw new OutOfBoundsException("No page type $type available.");
        }
    }
}


