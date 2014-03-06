<?php
require_once 'AFS/SEARCH/afs_facet_value_formatter.php';

/** @brief Retrieves facet helper from list of helpers. */
class AfsFacetHelperRetriever
{
    /** @brief Retrieves helper with specified facet identifier.
     *
     * @param $id [in] Facet identifier to look for.
     * @param $facet_helpers [in] List of helpers to consider.
     *
     * @return Requested helper or null when not found.
     */
    public static function get_helper($id, array& $facet_helpers)
    {
        $helper = null;
        if (array_key_exists($id, $facet_helpers))
            $helper = $facet_helpers[$id];
        else {
            foreach ($facet_helpers as $facet_helper) {
                if ($id == $facet_helper->get_id()) {
                    $helper = $facet_helper;
                    break;
                }
            }
        }
        return $helper;
    }

    /** @brief Retrieves facet value formatter.
     *
     * Formatters are needed to surround facet value with double quotes for
     * specific facet type and layout.
     *
     * @param $facet_id [in] Facet identifier.
     * @param $config [in] Configuration which should contain facet information.
     *
     * @return appropriate formatter.
     */
    public static function get_formatter($facet_id, $config)
    {
        try {
            $facet = $config->get_facet_manager()->get_facet($facet_id);
            if ((AfsFacetType::STRING_TYPE == $facet->get_type()
                    || AfsFacetType::DATE_TYPE == $facet->get_type())
                    && AfsFacetLayout::TREE == $facet->get_layout()) {
                return new AfsQuoteFacetValueIdFormatter();
            }
        } catch (Exception $e) { }

        return new AfsNoFacetValueIdFormatter();
    }
}
