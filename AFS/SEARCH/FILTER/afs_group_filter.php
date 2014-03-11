<?php
require_once 'AFS/SEARCH/FILTER/afs_combinable_filter.php';


/** @brief Helper function to create new AfsGroup instance.
 * @param $filter_expr [in] One filter or combined and/or grouped filters.
 * @return newly created AfsGroup instance.
 */
function group($filter_expr)
{
    return new AfsGroupFilter($filter_expr);
}


/** @brief Class used to group filter expressions.
 *
 * Example:
 * @code GROUP(filter_1 AND filter_2) OR filter_1 @endcode
 */
class AfsGroupFilter extends AfsCombinableFilter
{
    private $filter_expr = null;


    /** @brief Constructs new group instance.
     * @param $filter_expr [in] Valid filter expression.
     */
    public function __construct($filter_expr)
    {
        $this->filter_expr = $filter_expr;
    }

    /** @brief Transforms this instance in its string representation.
     * @return string representation of the instance.
     */
    public function to_string()
    {
        return '(' . $this->filter_expr->to_string() . ')';
    }
}
