<?php
/** @file full_lightweight_example.php
 * @example full_lightweight_example.php
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

/** [Search init] */
// Third parameter is set to AfsServiceStatus::STABLE by default.
$search = new AfsSearch('eval.partners.antidot.net', 70000);
/** [Search init] */

/** [Query init] */
// Instead, you can initialize new query and call $search->set_query($initial_query)
$search->build_query_from_url_parameters();
/** [Query init] */

/** [Query execution] */
// Default format is AfsHelperFormat::ARRAYS, it can be set to AfsHelperFormat::HELPERS
// when execute method is called.
$reply = $search->execute();
/** [Query execution] */

// Load and apply PHP templates
/** [Twig template] */
$template = $twig->loadTemplate('meta_template.html');
echo $template->render($reply);
/** [Twig template] */
?>
