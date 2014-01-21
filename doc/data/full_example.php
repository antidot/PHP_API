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

// Facets and Facet Manager
/** [Facets and Facet manager] */
$facet_mgr = new AfsFacetManager();
$facet_mgr->add_facet(new AfsFacet('Organization', AFS_FACET_STRING));
$facet_mgr->add_facet(new AfsFacet('date_parution', AFS_FACET_DATE));
$facet_mgr->add_facet(new AfsFacet('geo', AFS_FACET_STRING));
$facet_mgr->add_facet(new AfsFacet('media', AFS_FACET_STRING));
$facet_mgr->add_facet(new AfsFacet('person', AFS_FACET_STRING));
$facet_mgr->add_facet(new AfsFacet('period', AFS_FACET_DATE));
$facet_mgr->add_facet(new AfsFacet('source', AFS_FACET_STRING));
$facet_mgr->add_facet(new AfsFacet('taxo_iptc', AFS_FACET_STRING));
$facet_mgr->add_facet(new AfsFacet('theme', AFS_FACET_STRING));
$facet_mgr->add_facet(new AfsFacet('type', AFS_FACET_STRING));
$facet_mgr->add_facet(new AfsFacet('pays', AFS_FACET_STRING));
/** [Facets and Facet manager] */

// Query Manager
/** [Query manager] */
$query_mgr = new AfsSearchQueryManager($connector, $facet_mgr);
$reply = $query_mgr->send($query);
/** [Query manager] */

// Response Helper
/** [Response helper] */
$helper = new AfsResponseHelper($reply, $facet_mgr, $query, $coder, AfsHelperFormat::ARRAYS);
/** [Response helper] */

// Load and apply PHP templates
/** [Twig template] */
$template = $twig->loadTemplate('meta_template.html');
echo $template->render($helper->format());
/** [Twig template] */
?>
