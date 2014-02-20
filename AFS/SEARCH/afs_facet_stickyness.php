<?php

/** @brief Stickyness of the facets.
 *
 * Specify whether facet values should appear in result stream even if
 * corresponding values does not currently match any result.
 */
abstract class AfsFacetStickyness
{
    /** @brief All facet values are present in reply even if no current
     * result corresponds to some of these values. */
    const STICKY = 'sticky';
    /** @brief Only relevant facet values are present in reply. */
    const NON_STICKY = 'non sticky';
}



