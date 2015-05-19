<?php
/** @file raw_example.php
 * @example raw_example.php
 */

require_once "PHP_API/afs_lib.php";

$search = new AfsSearch('http://poc-afsstore.afs-antidot.net/', 30005);

$query = $search->build_query_from_url_parameters();
$query = $query->set_lang('fr');  // language is set manually in order to get spellcheck results
$query = $query->set_multi_selection_facets('classification');
$query = $query->set_mono_selection_facets('afs:lang', 'has_variants', 'has_image');
$query = $query->set_facet_order('price_eur', 'marketing', 'classification', 'has_variants', 'has_image');
$query = $query->set_facets_values_sort_order(AfsFacetValuesSortMode::ITEMS, AfsSortOrder::DESC);

$helper = $search->execute($query);
$generated_url = $search->get_generated_url();

$clustering_is_active = $query->has_cluster();
$nsmap = array('ns' => 'http://ref.antidot.net/store/afs#');

for ($helper->get_promotes() => $promote) {
    $clientdata = $promote->get_custom_data();
}
