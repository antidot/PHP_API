<?php
/** @file full_example.php
 * @example full_example.php
 */

require_once("afs_lib.php");

// To make this test work, you need to dowload and install Twig 
// (http://twig.sensiolabs.org/) 
// You shoud addapt following paths
require_once "/var/www/php-full-example/Twig-1.13.2/lib/Twig/Autoloader.php";
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('/var/www/php-full-example/templates');
$twig = new Twig_Environment($loader, array('debug' => true));
$twig->addExtension(new Twig_Extension_Debug());

// Coder/Decoder and Query
/** [Coder/decoder and Query] */
$coder = new AfsQueryCoder('full_example.php');
$query = $coder->build_query($_GET);
/** [Coder/decoder and Query] */

// Connector
/** [Connector] */
$service = new AfsService(70000, AFS_PAF_STABLE);
$url = 'http://eval.partners.antidot.net/search';
$connector = new AfsSearchConnector($url, $service);
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
/** [Reponse helper] */
$helper = new AfsResponseHelper($reply, $facet_mgr, $query, $coder, AFS_ARRAY_FORMAT);
/** [Reponse helper] */

// Load and apply PHP templates
$template = $twig->loadTemplate('meta_template.html');
echo $template->render($helper->replyset);
?>
