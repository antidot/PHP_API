<?php

require_once "COMMON/afs_tools.php";

/** @brief Defines various query origins.
 *
 * Origin of the query is set automatically by the query itself or various
 * helpers (such as AfsSpellcheckHelper). You are encouraged to set it manually
 * when you do not use queries provided by the different available helpers.
 */
class AfsOrigin extends BasicEnum
{
    private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

    private static $cache = null;

    /** @brief Direct access
     * @{ */

    /** @brief Direct access to specific item.
     */
    const DIRECT = 'DIRECT';
    /** @} */

    /** @name Items in the page
     * @{ */

    /** @brief Query originates from search box.
     *
     * This value is automatically set when new search words are set to the
     * query. */
    const SEARCHBOX = 'SEARCHBOX';
    /** @brief Query originates from suggestion. */
    const ACP = 'ACP';
    /** @brief Query originates from facet value.
     *
     * This value is automatically set when new filter value is set or removed. */
    const FACET = 'FACET';
    /** @brief Query originates from page change.
     *
     * This value is automatically set when new result page is selected. */
    const PAGER = 'PAGER';
    const SORT_ORIG = 'SORT_ORIG';
    /** @brief Preferences such as number of replies per page. */
    const PREFERENCES = 'PREFERENCES';
    const RESULT = 'RESULT';
    /** @} */

    /** @name Agents
     * @{ */

    /** @brief Query originates from spellcheck proposal.
     *
     * This value is automatically for queries generated from spellcheck results. */
    const SPELLCHECK = 'SPELLCHECK';
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
    const ACC = 'ACC';
    /** @} */

    /** @name E-commerce specific
     * @{ */
    const SHOPPING_CART = 'SHOPPING_CART';
    const PRODUCT_DESCRIPTION = 'PRODUCT_DESCRIPTION';
    /** @brief List of products in search result. */
    const PRODUCTS_FROM_SEARCH = 'PRODUCTS_FROM_SEARCH';
    /** @brief List of products in navigation. */
    const PRODUCTS_FROM_NAVIGATION = 'PRODUCTS_FROM_NAVIGATION';
    const PRODUCTS_UPSELL = 'PRODUCTS_UPSELL';
    const PRODUCTS_CROSS_SELL = 'PRODUCTS_CROSS_SELL';
    const PRODUCTS_RECOMMENDED = 'PRODUCTS_RECOMMENDED';
    const PRODUCTS_SIMILAR = 'PRODUCTS_SIMILAR';
    const PRODUCTS_VIEWED = 'PRODUCTS_VIEWED';
    const PRODUCTS_BOUGHT = 'PRODUCTS_BOUGHT';
    /** @} */

    /** @name SEO index
     * @{ */
    const SEO_INDEX = 'SEO_INDEX';
    /** @} */

    /** @name User specific
     * @{ */
    const USER_1 = 'USER_1';
    const USER_2 = 'USER_2';
    const USER_3 = 'USER_3';
    const USER_4 = 'USER_4';
    const USER_5 = 'USER_5';
    const USER_6 = 'USER_6';
    const USER_7 = 'USER_7';
    const USER_8 = 'USER_8';
    const USER_9 = 'USER_9';
    /** @} */
}


