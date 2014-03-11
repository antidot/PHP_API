<?php
require_once 'AFS/SEARCH/FILTER/afs_combinable_filter.php';


/** @brief Filter with filter value.
 *
 * This object is made of facet identifier, filter operator and a value.
 */
class AfsValuedFilter extends AfsCombinableFilter
{
    private $op_filter = null;
    private $value = null;


    /** @brief Constructs new valued filter object.
     *
     * @param $op_filter [in] Filter operator object (see AfsOperatorFactory).
     * @param $value [in] The value to consider (to filter on when operator is
     *        set to equal).
     */
    public function __construct(AfsBaseOperatorFilter $op_filter, $value)
    {
        $this->op_filter = $op_filter;
        $this->value = $value;
    }

    /** @brief Transforms this instance in its string representation.
     * @return string representation of the instance.
     */
    public function to_string()
    {
        return $this->op_filter->to_string() . $this->value;
    }
}
