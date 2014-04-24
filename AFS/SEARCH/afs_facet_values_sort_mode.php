<?php
require_once 'COMMON/afs_tools.php';

/** @brief Facet values sort mode enumerator. */
class AfsFacetValuesSortMode extends BasicEnum
{
 		private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

   /** @brief Alphabetical sort order on facet value ids. */
    const ALPHA = 'alpha';
    /** @brief Numerical sort order on number of items per facet value. */
    const ITEMS = 'items';
    /** @brief Alphabetical sort order using user specified key. */
    const ALPHA_KEY = 'alphaKey';
    /** @brief Numerical sort order using user specified key. */
    const NUM_KEY = 'numKey';
}
