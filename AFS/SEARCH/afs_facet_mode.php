<?php
require_once 'COMMON/afs_tools.php';

/** @brief Mode of the facets
 *
 * Specify the mode of the facets. Modes allow to combine or replace values of
 * the facets.
 */
class AfsFacetMode extends BasicEnum
{
    private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

   /** @brief Single mode.
     *
     * New value set for the facet replaces existing one.
     *
     * Example:<br/>
     * Let's suppose your products have one brand each.
     *
     * - A query without any filter should get a reply with all products and
     * following facet values:
     *
     *     - Brand1  (3 products)
     *     - Brand2  (7 products)
     *     - Brand3  (4 products)
     *
     * - Then you can filter on Brand2, reply should only contains products
     * of appropriate brand whereas facet values are not changed. All the brands
     *are still available so that one can filter on other brand.
     *
     *     - Brand1  (3 products)
     *     - Brand2  (7 products) X
     *     - Brand3  (4 products)
     *
     *
     * - You can filter on Brand1 so that you get all products for this brand
     * whereas facet values are still unchanged:
     *
     *     - Brand1  (3 products) X
     *     - Brand2  (7 products)
     *     - Brand3  (4 products)
     */
    const SINGLE_MODE = 'SINGLE_MODE';

    /** @brief Or mode.
     *
     * New value set for the facet is appended to the list of values already
     * set. All the values are or-combined.
     *
     * Example:<br/>
     * Let's suppose your products have one brand each.
     *
     * - A query without any filter should get a reply with all products and
     * following facet values:
     *
     *     - Brand1  (3 products)
     *     - Brand2  (7 products)
     *     - Brand3  (4 products)
     *
     * - Then you can filter on Brand2, reply should only contains products
     * of appropriate brand whereas facet values are not changed. All the brands
     * are still available so that one can filter on other brand.
     *
     *     - Brand1  (3 products)
     *     - Brand2  (7 products) X
     *     - Brand3  (4 products)
     *
     * - You can add filter on Brand1 so that you get all products for brand
     * 1 and brand 2  whereas facet values are still unchanged:
     *
     *     - Brand1  (3 products) X
     *     - Brand2  (7 products) X
     *     - Brand3  (4 products)
     */
    const OR_MODE = 'OR_MODE';

    /** @brief And mode.
     *
     * This is the standard mode for Antidot search engine.
     *
     * New value set for the facet is appended to the list of values already
     * set. All the values are and-combined.
     *
     * Example:<br/>
     * Let's suppose your products have one or more colors each.
     *
     * - A query without any filter should get a reply with all products and
     * following facet values:
     *
     *     - Green  (3 products)
     *     - Blue   (7 products)
     *     - Red    (4 products)
     *
     * - Then you can filter on Blue, reply should only contains products
     * of appropriate color and facet values are updated. Only relevant facet
     * values are present in the reply: let's suppose there is no product which
     * is blue and red and there is only two products which are blue and green:
     *
     *     - Green  (2 products)
     *     - Blue   (7 products) X
     *
     * - You can add filter on Green so that you get all products which are
     * blue and green. Facet values are updated according to this new filter:
     *
     *     - Green  (2 products) X
     *     - Blue   (2 products) X
     */
    const AND_MODE = 'AND_MODE';

    /** @brief Or mode.
     *
     * New value set for the facet is appended to the list of values already
     * set. All the values are and-combined.
     *
     * Example:<br/>
     * Let's suppose your products have colors.
     *
     * - A query without any filter should get a reply with all products and
     * following facet values:
     *
     *     - Red    (3 products)
     *     - Green  (7 products)
     *     - Blue   (4 products)
     *
     * - Then you can filter on Red, reply should only contains Red products
     *  whereas facet values are not changed. All the colors
     * are still available so that one can filter on other color.
     *
     *     - Red    (3 products) X
     *     - Green  (7 products)
     *     - Blue   (4 products)
     *
     * - You can add filter on Green so that you get all products that are Red And Green
     *   whereas facet values are still unchanged:
     *
     *     - Red    (3 products) X
     *     - Green  (7 products) X
     *     - Blue   (4 products)
     */
    const STICKY_AND_MODE = 'STICKY_AND_MODE';

    /** @brief Unspecified mode.
     *
     * When a facet is built with unspecified mode, global mode defined at
     * facet manager level is applied.
     */
    const UNSPECIFIED_MODE = 'UNSPECIFIED_MODE';
}


