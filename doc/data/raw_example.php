<?php
/** @file raw_example.php
 * @example raw_example.php
 */

require_once "/home/ct/Dev/PHP_API/afs_lib.php";

$search = new AfsSearch('172.17.0.2', 10, AfsServiceStatus::RC);
$_SERVER['QUERY_STRING'] = 'afs%3Areplies=120&afs%3Aquery=banner&afs:service=7225';

$search->build_query_from_url_parameters();
$query = $search->get_query();
$feed = 'Catalog';
/* get all facet ids of filters setted on curent query
foreach ($query->get_filters() as $facet_id) {
    try {
        // get values of filter
        // if filter is aleady contextualized, this will throw an exception AfsFilterException
        // because correct call to get contextualized filter values should be get_filter_values($facet_id, $feed)
        $filter_values = $query->get_filter_values($facet_id);
        // re-contextualized all filter and remove uncontextualized ones
        foreach ($filter_values as $value) {
            $query = $query->remove_filter($facet_id, $value);
            $query = $query->add_filter_on_feed($facet_id, array($value), $feed);
        }
    } catch (AfsFilterException $e) {

    }
}*/

$search->set_query($query);

$helper = $search->execute();


/*
$query = $search->build_query_from_url_parameters();
$query = $query->set_lang('fr');  // language is set manually in order to get spellcheck results
$query = $query->set_multi_selection_facets('classification');
$query = $query->set_mono_selection_facets('afs:lang', 'has_variants', 'has_image');
$query = $query->set_facet_order('price_eur', 'marketing', 'classification', 'has_variants', 'has_image');
$query = $query->set_facets_values_sort_order(AfsFacetValuesSortMode::ITEMS, AfsSortOrder::DESC);
*/


$helper = $search->execute($query);
$generated_url = $search->get_generated_url();

$clustering_is_active = $query->has_cluster();
$nsmap = array('ns' => 'http://ref.antidot.net/store/afs#');




/*








foreach ($facet->get_elements() as $value) {


            <a class="list-group-item <?php echo ($value->active ? 'active' : '') ?>" href="<?php echo $value->link ?>"><?php echo $value->label ?><span class="badge"><?php echo $value->count ?></span></a>
                    <!-- $active = $query->has_filter($facet->id, $value->key);
                      ...
*/