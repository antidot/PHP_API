<?php
require_once 'COMMON/afs_tools.php';

/** @brief Mode of the facets
 *
 * Specify the mode of the facets. Modes allow to combine or replace values of
 * the facets.
 */
abstract class AfsFacetMode extends BasicEnum
{
    /** @brief Replace mode.
     *
     * New value set for the facet replace existing one. */
    const REPLACE = 'replace';
    /** @brief Add mode.
     *
     * New value set for the facet is appended to the list of already set facets. */
    const ADD = 'add';
}


