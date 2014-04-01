<?php
require_once 'AFS/SEARCH/afs_facet_values_sort_mode.php';
require_once 'AFS/SEARCH/afs_sort_order.php';

/** @brief Facet values sort order. */
class AfsFacetValuesSortOrder
{
    /** @brief Sort order mode.
     *
     * See AfsFacetValuesSortMode for available values.
     */
    public $mode = null;
    /** @brief Sort order.
     *
     * See AfsSortOrder for available values.
     */
    public $order = null;

    /** @brief Constructs new instance with appropriate sort mode/order.
     *
     * @param $mode [in] Sort mode (see AfsFacetValuesSortMode for details).
     * @param $order [in] Sort order (see AfsSortOrder for details).
     */
    public function __construct($mode, $order)
    {
        AfsFacetValuesSortMode::check_value($mode, 'Invalid facet values sort mode: ');
        AfsSortOrder::check_value($order, 'Invalid facet values sort order: ');
        $this->mode = $mode;
        $this->order = $order;
    }

    /** @brief Format instance as array of sort mode then sort order.
     * @return formatted array.
     */
    public function format()
    {
        return array($this->mode, $this->order);
    }
}
