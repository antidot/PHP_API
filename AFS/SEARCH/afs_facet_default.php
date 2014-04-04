<?php

require_once 'AFS/SEARCH/afs_query_object_interface.php';
require_once 'AFS/SEARCH/afs_facet_values_sort_order.php';

/** @brief Manage parameter for all facets.
 */
class AfsFacetDefault implements AfsQueryObjectInterface
{
    private $replies = 1000;
    private $values_sort_order = null;


    /** @brief Constructs new AfsFacetDefault instance.
     *
     * @param $other [in] Other instance used to initialize newly created one
     *        (default: @c null, creates new instance from scratch).
     */
    public function __construct(AfsFacetDefault $other=null)
    {
        if (! is_null($other)) {
            $this->replies = $other->replies;
            if (! is_null($other->values_sort_order))
                $this->values_sort_order = $other->values_sort_order->copy();
        }
    }

    /** @name Facet values replies
     * @{ */

    /** @brief Defines number of facet values in facet replies.
     *
     * Default number of facet values per facet is 1000. This value overrides
     * default AFS search engine value which is 10.
     *
     * @param $nb_replies [in] Maximum number of facet values.
     */
    public function set_nb_replies($nb_replies)
    {
        $this->replies = $nb_replies;
    }
    /** @brief Retrieves maximum number of facet values in facet replies.
     * @return maximum number of facet values.
     */
    public function get_nb_replies()
    {
        return $this->replies;
    }
    /**  @} */

    /** @name Facet values sort order
     * @{ */

    /** @brief Defines sort order for all facet values.
     *
     * AFS search default sort for facet values is alphanumeric. This method
     * allows to change this behaviour.
     *
     * @param $mode [in] Sort mode (see AfsFacetValuesSortMode).
     * @param $order [in] Sort order (see AfsSortOrder).
     *
     * @exception InvalidArgumentException when $mode or $order is invalid.
     */
    public function set_sort_order($mode, $order)
    {
        $this->values_sort_order = new AfsFacetValuesSortOrder($mode, $order);
    }
    /** @brief Retrieves sort order defined on facet values.
     * @return sort order or @c null when no sort sorder has been set.
     */
    public function get_sort_order()
    {
        return $this->values_sort_order;
    }
    /**  @} */

    /** @name Interface implementation
     * @{ */

    /** @brief Produces new instance copied from current one.
     * @return copy of the current instance.
     */
    public function copy()
    {
        return new AfsFacetDefault($this);
    }
    /** @brief Format object to appropriate string form.
     * @return array of strings.
     */
    public function format()
    {
        $result = array('replies=' . $this->replies);
        if (! is_null($this->values_sort_order)) {
            $result[] = 'sort=' . $this->values_sort_order->mode;
            $result[] = 'order=' . $this->values_sort_order->order;
        }
        return $result;
    }
    /** @} */
}
