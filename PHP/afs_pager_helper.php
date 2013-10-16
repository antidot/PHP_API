<?php
require_once "afs_helper_base.php";

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
    private $query = null;
    private $coder = null;

    /** @brief Construct helper with pager and current query.
     *
     * @param $pager [in] pager retrieved from reply.
     * @param $query [in] current @a AfsQuery which will be used to generate
     *        appropriate queries (see bellow @a get_pages, @a get_previous and
     *        @a get_next).
     * @param $coder [in] @a AfsQueryCoderInterface if set it will be used to
     *        create links instead of queries (default: null).
     *
     * @exception InvalidArgumentException @a pager is invalid.
     */
    public function __construct($pager, AfsQuery $query,
        AfsQueryCoderInterface $coder=null)
    {
        if (! property_exists($pager, 'currentPage')) {
            throw new InvalidArgumentException('Pager is of the wrong type.');
        }
        $this->pager = $pager;
        $this->query = $query;
        $this->coder = $coder;
    }

    /** @brief Retrieve all pages.
     *
     * List all pages in ascending order. A query or a link is associated with
     * each page depending whether no coder or valid one has been provided.
     *
     * @return array of page => query or link.
     */
    public function get_pages()
    {
        $result = array();
        foreach ($this->pager->page as $page) {
            $query = $this->query->set_page($page);
            $result[$page] = is_null($this->coder) ? $query
                : $this->coder->generate_link($query);
        }
        return $result;
    }

    /** @brief Retrieve current page number.
     * @return Current page number.
     */
    public function get_current_no()
    {
        return $this->pager->currentPage;
    }

    /** @brief Retrieve query for previous page.
     * @return query for previous page.
     * @exception OutOfBoundsException when there is no previous page.
     */
    public function get_previous()
    {
        return $this->get_typed('previousPage');
    }

    /** @brief Retrieve query for next page.
     * @return query for next page.
     * @exception OutOfBoundsException when there is no next page.
     */
    public function get_next()
    {
        return $this->get_typed('nextPage');
    }

    /** @brief Retrieve pages as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li <tt>&lt;page number></tt>: query or link associated to this page
     * number,
     * @li @c next: query or link for the next page (if it exists),
     * @li @c previous: query or link for the previous page (if it exists),
     * @li @c current: current page number.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        $pages = array();
        try {
            $pages['previous'] = $this->get_previous();
        } catch (OutOfBoundsException $e) { }
        $pages += $this->get_pages();
        try {
            $pages['next'] = $this->get_next();
        } catch (OutOfBoundsException $e) { }

        return array('pages' => $pages,
                     'current' => $this->get_current_no());
    }

    private function get_typed($type)
    {
        if (property_exists($this->pager, $type)) {
            $query = $this->query->set_page($this->pager->$type);
            return is_null($this->coder) ? $query
                : $this->coder->generate_link($query);
        } else {
            throw new OutOfBoundsException("No page type $type available.");
        }
    }
}

?>
