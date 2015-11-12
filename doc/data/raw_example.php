<?php
/** @file raw_example.php
 * @example raw_example.php
 */
require_once "/home/ct/Dev/PHP_API/afs_lib.php";
require_once "AFS/SEARCH/afs_introspection.php";

$search = new AfsSearch('3suisses-be.afs-antidot.net/', 7123);
$intropsector = new AfsIntrospection($search);
$metadata = $intropsector->get_feed_metadata('Catalog');

$filters = $metadata->get_filters_info();
$facets= $metadata->get_facets_info();
$query = $search->build_query_from_url_parameters();
$helper = $search->execute($query);
$generated_url = $search->get_generated_url();
$clustering_is_active = $query->has_cluster();
$nsmap = array('ns' => 'http://ref.antidot.net/store/afs#');

