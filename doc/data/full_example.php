<?php
/** @file full_example.php
 * @example full_example.php
 */

/** [Include lib] */
require_once "PHP_API/afs_lib.php";
/** [Include lib] */

/** [Twig init] */
// You shoud addapt following paths
require_once "/var/www/php-example/Twig-1.15.0/lib/Twig/Autoloader.php";
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('/var/www/php-example/templates');
$twig = new Twig_Environment($loader, array('debug' => true));
$twig->addExtension(new Twig_Extension_Debug());
/** [Twig init] */

// Coder/Decoder and Query
/** [Coder/decoder and Query] */
$coder = new AfsQueryCoder('full_example.php');
$query = $coder->build_query($_GET);
/** [Coder/decoder and Query] */

// Connector
/** [Connector] */
$service = new AfsService(70000, AFS_PAF_STABLE);
$host = 'eval.partners.antidot.net';
$connector = new AfsSearchConnector($host, $service);
/** [Connector] */

// Configuration
/** [Helper configuration] */
$config = new AfsHelperConfiguration();
$config->set_query_coder($coder);
/** [Helper configuration] */

// Facets and Facet Manager
/** [Facets and Facet manager] */
$facet_mgr = $config->get_facet_manager();
$facet_mgr->add_facet(new AfsFacet('Organization', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('date_parution', AfsFacetType::DATE_TYPE));
$facet_mgr->add_facet(new AfsFacet('geo', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('media', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('person', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('period', AfsFacetType::DATE_TYPE, AfsFacetLayout::INTERVAL));
$facet_mgr->add_facet(new AfsFacet('source', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('taxo_iptc', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('theme', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('type', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('pays', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('annotated_city', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('buildingDate', AfsFacetType::DATE_TYPE, AfsFacetLayout::INTERVAL));
$facet_mgr->add_facet(new AfsFacet('date', AfsFacetType::DATE_TYPE));
$facet_mgr->add_facet(new AfsFacet('stationParRegion', AfsFacetType::STRING_TYPE));
$facet_mgr->add_facet(new AfsFacet('ticketPrice', AfsFacetType::INTEGER_TYPE, AfsFacetLayout::INTERVAL));
/** [Facets and Facet manager] */

// Query Manager
/** [Query manager] */
$query_mgr = new AfsSearchQueryManager($connector, $facet_mgr);
$reply = $query_mgr->send($query);
/** [Query manager] */

// Response Helper
/** [Response helper] */
$helper = new AfsResponseHelper($reply, $query, $config);
/** [Response helper] */

// Load and apply PHP templates
/** [Twig template] */
$template = $twig->loadTemplate('meta_template.html');
echo $template->render($helper->format());
/** [Twig template] */
?>
