<?php
require_once 'COMMON/afs_tools.php';

/** @brief Combination of the facets' values.
 *
 * Specify whether results of each filter should be summed up or instersected to
 * build final result.
 */
class AfsFacetCombination extends BasicEnum
{
		private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

    /** @brief Values of the facets are OR-combined. */
    const OR_MODE = 'or';
    /** @brief Values of the facets are AND-combined. */
    const AND_MODE = 'and';
}


