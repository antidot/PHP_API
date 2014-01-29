<?php
require_once 'COMMON/afs_tools.php';

/** @brief Layout of the facets
 *
 * Specify the layout of the facets.
 */
abstract class AfsFacetLayout extends BasicEnum
{
    /** @brief Tree layout.
     *
     * This layout is used for flat and hierarchical facet values. */
    const TREE = 'TREE';
    /** @brief Interval layout.
     *
     * This layout is used for interval of values such as prices. */
    const INTERVAL = 'INTERVAL';
}


