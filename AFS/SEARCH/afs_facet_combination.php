<?php

/** @brief Combination of the facets' values.
 *
 * Specify whether results of each filter should be summed up or instersected to
 * build final result.
 */
abstract class AfsFacetCombination
{
    /** @brief Values of the facets are OR-combined. */
    const OR_MODE = 'or';
    /** @brief Values of the facets are AND-combined. */
    const AND_MODE = 'and';
}


