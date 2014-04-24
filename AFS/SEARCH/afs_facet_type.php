<?php
require_once 'COMMON/afs_tools.php';

/** @brief Type of the facets
 *
 * Specify the type of the facets.
 */
class AfsFacetType extends BasicEnum
{
 		private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

   /** @brief Facet values of type integer. */
    const INTEGER_TYPE = 'INTEGER';
    /** @brief Facet values of type real. */
    const REAL_TYPE = 'REAL';
    /** @brief Facet values of type string. */
    const STRING_TYPE = 'STRING';
    /** @brief Facet values of type date. */
    const DATE_TYPE = 'DATE';
    /** @brief Facet values of type boolean. */
    const BOOL_TYPE = 'BOOL';
    /** @brief Unknown facet type. */
    const UNKNOWN_TYPE = 'UNKNOWN';
}


