<?php
/** @file afs_filter.php */

require_once 'AFS/SEARCH/FILTER/afs_operator_filter.php';
require_once 'AFS/SEARCH/FILTER/afs_native_function_filter.php';

/** @brief Helper function to create new AfsFilter instance.
 * @param $id [in] Filter identifier.
 * @return newly created AfsFilter instance.
 */
function filter($id)
{
    return new AfsFilterWrapper(null, new AfsFilter($id));
}


/** @brief Base class used to represent a filter.
 *
 * This class should never be instanced directly. Use filter function instead.
 */
class AfsFilter
{
    private $id = null;


    /** @brief Constructs new filter instance.
     * @param $id [in] Filter identifier (should be a string).
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /** @brief Create new filter operator object.
     *
     * Valid operators are:
     * - @c equal: equal comparison,
     * - @c not_equal: not equal comparison,
     * - @c less: less than comparison,
     * - @c less_equal: less than or equal comparison,
     * - @c greater: greater than comparison,
     * - @c greater_equal: greater than or equal comparison.
     *
     * @param $name [in] Should be one of the valid operators.
     *
     * @return newly created instance depending on the provided parameter.
     *
     * @exception AfsUnknownOperatorException when invalid operator has been
     *            provided.
     */
    public function __get($name)
    {
        return AfsOperatorFactory::create($name, $this);
    }

    /** @brief Transforms this instance in its string representation.
     * @return string representation of the instance.
     */
    public function to_string()
    {
        return $this->id;
    }
}
