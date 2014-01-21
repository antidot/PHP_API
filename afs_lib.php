<?php
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once 'AFS/SEARCH/afs_search_connector.php';
require_once 'AFS/SEARCH/afs_query.php';
require_once 'AFS/SEARCH/afs_facet.php';
require_once 'AFS/SEARCH/afs_search_query_manager.php';

require_once 'AFS/SEARCH/afs_response_helper.php';
require_once 'AFS/SEARCH/afs_query_coder.php';

require_once 'AFS/SEARCH/afs_text_visitor.php';
?>
