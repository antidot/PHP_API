<?php

/** @brief Defines various query origins.
 *
 * Origin of the query is set automatically by the query itself or various
 * helpers (such as AfsSpellcheckHelper). You are encouraged to set it manually
 * when you do not use queries provided by the different available helpers.
 */
abstract class AfsOrigin extends BasicEnum
{
    /** @brief Query originates from search box.
     *
     * This value is automatically set when new search words are set to the
     * query. */
    const SEARCHBOX = 'SEARCHBOX';
    /** @brief Query originates from spellcheck proposal.
     *
     * This value is automatically for queries generated from spellcheck results. */
    const SPELLCHECK = 'SPELLCHECK';
    /** @brief Query originates from facet value.
     *
     * This value is automatically set when new filter value is set or removed. */
    const FACET = 'FACET';
    /** @brief Query originates from page change.
     *
     * This value is automatically set when new result page is selected. */
    const PAGER = 'PAGER';
    /** @brief Query originates from related searches.
     *
     * This value is automatically set when one of the related search results is
     * selected. */
    const RTE = 'RTE';
    /** @brief Query originates from one of the interpretation of the query.
     *
     * This value is automatically set when one of the concept results is selected.
     */
    const CONCEPT = 'CONCEPT';
    /** @brief Query originates from advertisement.
     *
     * This value is automatically set when one of the promote results is selected.
     */
    const PROMOTE = 'PROMOTE';
}

?>
