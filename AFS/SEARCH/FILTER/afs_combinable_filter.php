<?php
require_once 'AFS/SEARCH/FILTER/afs_combinator_filter.php';


/** @brief Base class for combinable elements.
 *
 * Combinable elements are element which can be combined with specific
 * combinator filter object such as: AfsAndCombinatorFilter and
 * AfsOrCombinatorFilter.
 */
abstract class AfsCombinableFilter
{
    /** @brief Creates new combinator filter object initialized with current instance.
     *
     * @param $name [in] Combinator name. Available values are:
     *        - @c and: to and-combine elements,
     *        - @c or: to or-combine elements.
     *
     * @return Newly created instance of combinator filter type.
     *
     * @exception AfsUnknownCombinatorException required combinator does not exist.
     */
    public function __get($name)
    {
        return AfsCombinatorFactory::create($name, $this);
    }
}
