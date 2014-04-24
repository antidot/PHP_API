<?php
require_once 'COMMON/afs_tools.php';

/** @brief Layout of the facets
 *
 * Specify the layout of the facets.
 */
class AfsFacetLayout extends BasicEnum
{
		private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

    /** @brief Tree layout.
     *
     * This layout is used for flat and hierarchical facet values. */
    const TREE = 'TREE';
    /** @brief Interval layout.
     *
     * This layout is used for interval of values such as prices. */
    const INTERVAL = 'INTERVAL';
    /** @brief Unknown layout used for not fully declared facets.
     *
     * This is intended for internal use only. */
    const UNKNOWN = 'UNKNOWN';
}


