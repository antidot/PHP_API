<?php
require_once 'COMMON/afs_tools.php';

/** @brief Type of the facets
 *
 * Specify the type of the facets.
 */
abstract class AfsFacetType extends BasicEnum
{
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
}


