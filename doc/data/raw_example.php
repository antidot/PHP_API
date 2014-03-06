<?php
/** @file raw_example.php
 * @example raw_example.php
 */

require_once "PHP_API/afs_lib.php";

$search = new AfsSearch('eval.partners.antidot.net', 48000);
$search->build_query_from_url_parameters();
/*
$query = $search->get_query();
$search->set_query($query->set_lang('fr'));  // language is set manually in order to get spellcheck results
*/
$search->set_facet_sort_order(array('price_eur', 'marketing'), AfsFacetSort::LAX);
$helper = $search->execute(AfsHelperFormat::HELPERS);
$generated_url = $search->get_generated_url();

$query = $search->get_query();
$clustering_is_active = $query->has_cluster();
$nsmap = array('ns' => 'http://ref.antidot.net/store/afs#');

?>


<html>
  <head>
    <title>Antidot PHP API - Raw example</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div>
    <a href="<?php echo $generated_url; ?>" target="_blank"><?php echo $generated_url; ?></a>
    </div>
    <div class="page-header">
      <h1>Raw example <small>based on the Antidot PHP API</small></h1>
    </div>
    <!-- ####################### Search box ########################### -->
    <div class="row">
      <div class="col-md-5"></div>
      <div class="input-group col-md-2">
        <form method="get" action="" role="form" class="input-group">
          <span class="input-group-addon">Search</span>
          <input type="search" name="query" class="form-control" placeholder="Keywords" />
          <span class="input-group-btn">
            <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search"></span></button>
          </span>
        </form>
      </div>
    </div>

    <!-- ####################### Current filter parameters ########################### -->
<?php
$params = $search->get_query()->get_parameters(false);
if (array_key_exists('filter', $params) && is_array($params['filter'])) {
    echo '
    <div class="row">
      <div class="col-md-8">';
    foreach ($params['filter'] as $filter => $values) {
        echo '
        <ul>Filter: <strong>' . $filter . '</strong>';
        foreach ($values as $value) {
            echo '
          <li>Value: ' . $value . '</li>';
        }
        echo '
        </ul>';
    }
    echo '
      </div>
    </div>';
} ?>

    <!-- ####################### Promote ########################### -->
<?php
if ($helper->has_promote()) {
    echo '
    <div class="row">
      <div class="col-md-3"></div>';
    foreach ($helper->get_promotes() as $promote) {
        echo '
          <div class="col-md-2">
            <h4><span class="label label-danger">' . $promote->get_title() . '</span></h4>
            <p>' . $promote->get_abstract() . '</p>
            <ul>Some custom data:
              <li>tag: ' . $promote->get_custom_data('tag') . '</li>
              <li>identifier: ' . $promote->get_custom_data('id') . '</li>
            </ul>
            <ul>Or loop on custom data:';
        foreach ($promote->get_custom_data() as $key => $value) {
            echo '
              <li><strong>' . $key . '</strong>: ' . $value . '</li>';
        }
        echo '
            </ul>
          </div>';
    }
    echo '
      </div>
    </div>';
} ?>

<?php
// checks whether there is at least one replyset
if ($helper->has_replyset()) {
    $replyset = $helper->get_replyset(); // Retrieves only first replyset

    if ($replyset->has_facet()) {
    /* if (in_array('facets', $replyset)) */
?>
    <div class="row">
    <div class="col-md-3">
      <h2>Filters</h2>
    <!-- ####################### Filters ########################### -->
<?php
foreach ($replyset->get_facets() as $facet) {
/* foreach ($replyset->facets->facet as $facet) */
?>
            <div class="panel panel-default">
            <div class="panel-heading"><?php echo $facet->get_label() ?></div> <!-- $facet->labels[0]->label -->
              <div class="panel-body">
                <div class="list-group">
                 <div class="list-group">
<?php
foreach ($facet->get_elements() as $value) {
/* $item = null;
 * if ($facet->{'afs:t'} == 'FacetTree') {
 *   $item = 'node';
 * } elif ($facet->{'afs:t'} == 'FacetInterval') {
 *   $item = 'interval;
 * } else {
 *   throw new Exception('Unknown facet layout: ' . $facet->{'afs:t'});
 * }
 * foreach ($facet->$item as $value) */
?>
            <a class="list-group-item <?php echo ($value->active ? 'active' : '') ?>" href="<?php echo $value->link ?>"><?php echo $value->label ?><span class="badge"><?php echo $value->count ?></span></a>
                    <!-- $active = $query->has_filter($facet->id, $value->key);
                      ...
                    -->
<?php } ?>
                 </div>
                </div>
              </div>
            </div>
<?php } ?>
    </div>
<?php } ?>

<div class="col-md-9">
  <div class="row page-header">
    <div class="col-md-1"></div>
    <div class="col-md-2">
    <h2>Results <span class="label label-success"><?php echo $replyset->get_meta()->get_total_replies() ?></span></h2>
      <h4><span class="label label-info">Duration <?php echo $replyset->get_meta()->get_duration() ?> ms</span></h4>
    </div>
    <div class="col-md-1"></div>
    <div class="col-md-2">
    <!-- ####################### Relevance ########################### -->
<?php
    $query = $search->get_query();
    if ($query->has_sort(AfsSortBuiltins::RELEVANCE)) {
        if ($query->get_sort_order(AfsSortBuiltins::RELEVANCE) == AfsSortOrder::ASC) {
            $relevance_icon = 'glyphicon-arrow-up';
            $relevance_query = $query->add_sort(AfsSortBuiltins::RELEVANCE, AfsSortOrder::DESC);
        } else {
            $relevance_icon = 'glyphicon-arrow-down';
            $relevance_query = $query->add_sort(AfsSortBuiltins::RELEVANCE, AfsSortOrder::ASC);
        }
    } else {
        $relevance_query = $query->add_sort(AfsSortBuiltins::RELEVANCE, AfsSortOrder::ASC);
        $relevance_icon = 'glyphicon-arrow-down';
    }
    $relevance_link = $search->get_helpers_configuration()->get_query_coder()->generate_link($relevance_query);
?>
    <a href="<?php echo $relevance_link ?>" class="btn btn-default btn-lg active" role="button"><span class="glyphicon <?php echo $relevance_icon; ?>"></span> Relevance</a>
    </div>
    <div class="col-md-2">
<?php
$query_coder = $search->get_helpers_configuration()->get_query_coder();
if ($clustering_is_active) {
    $cluster_query = $query->unset_cluster();
    $cluster_link = $query_coder->generate_link($query->unset_cluster());
    $cluster_label = 'Remove clusters';
} else {
    $cluster_link = $query_coder->generate_link($query->set_cluster('marketing', 1)->set_overspill());
    $cluster_label = 'Create cluster on "marketing" filter';
} ?>
      <a href="<?php echo $cluster_link ?>" class="btn btn-default btn-lg active" role="button"><?php echo $cluster_label ?></a>
    </div>
  </div>

    <!-- ####################### Clusters ########################### -->
<?php
if ($clustering_is_active) {
    foreach ($replyset->get_clusters() as $cluster) {
        echo '
  <div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-5">
      <h3>
        <span class="label label-success">
          <a href="' . $query_coder->generate_link($cluster->get_query()) . '">' . $cluster->get_label() . '<a>
        </span>
      </h3>
    </div>
  </div>
  <ul class="list-unstyled">';
        foreach ($cluster->get_replies() as $reply) {
            echo '
          <li>
              <h3>' . $reply->get_title() . '</h3>
              <p><a href="' . $reply->get_uri() . '">' . $reply->get_uri() . '</a></p>
              <p>' . $reply->get_abstract() . '</p>
              <p>Some client data:
                <ul>
                  <li>Name: ' . $reply->get_clientdata()->get_value('/ns:product/ns:name', $nsmap) . '</li>
                  <li>Availability: ' . $reply->get_clientdata()->get_value('/ns:product/ns:is_available', $nsmap) . '</li>
                  <li>Prices:
                    <ul>';
// Here multiple values are retrieved from client data
foreach ($reply->get_clientdata()->get_values('/ns:product/ns:prices/ns:price', $nsmap) as $value)
    echo '<li>' . $value . '</li>';
echo '
                    </ul>
                  </li>
                </ul>
              </p>
          </li>';
        }
    echo '
  </ul>';
    }

    echo '
  <div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-5">
      <h3><span class="label label-success">Other results</span></h3>
    </div>
  </div>';
} ?>

    <!-- ####################### Replies ########################### -->
<?php
echo '
  <ul class="list-unstyled">';
foreach ($replyset->get_replies() as $reply) {
?>
          <li>
              <h3><?php echo $reply->get_title() ?></h3>
              <p><a href="<?php echo $reply->get_uri() ?>"><?php echo $reply->get_uri() ?></a></p>
              <p><?php echo $reply->get_abstract() ?></p>
              <p>Some client data:
                <ul>
                  <li>Name: <?php echo $reply->get_clientdata()->get_value('/ns:product/ns:name', $nsmap) ?></li>
                  <li>Availability: <?php echo $reply->get_clientdata()->get_value('/ns:product/ns:is_available', $nsmap) ?></li>
                  <li>Prices:
                    <ul>
<?php
// Here multiple values are retrieved from client data
foreach ($reply->get_clientdata()->get_values('/ns:product/ns:prices/ns:price', $nsmap) as $value) {
    echo '<li>' . $value . '</li>';
} ?>
                    </ul>
                  </li>
                </ul>
              </p>
          </li>
<?php } ?>
  </ul>
</div>
    </div>

    <!-- ####################### Pager ########################### -->
<?php
if ($replyset->has_pager()) {
    $pager = $replyset->get_pager();
?>
    <div class="row">
      <div class="row">
        <div class="col-md-5"></div>
        <div class="input-group col-md-3">
          <ul class="pagination">
<?php
foreach ($pager->get_all_pages() as $page => $url) {
    if ($page == $pager->get_current_no()) {
        $active = 'active';
    } else {
        $active = '';
    }
?>
            <li class="<?php echo $active ?>"><a href="<?php echo $url ?>"><?php echo $page ?></a></li>
<?php } ?>

          </ul>
        </div>
      </div>
<?php } ?>

    </div>

    <!-- ####################### Spellcheck ########################### -->
<?php } elseif ($helper->has_spellcheck()) { // if no replyset, let's check spellcheck ?>
    <div class="row">
      <div class="col-md-9">
        <div class="row">
          <div class="col-md-1"></div>
          <div class="col-md-4">
            <h2>Typo ?</h2>
          </div>
        </div>
<?php
foreach ($helper->get_spellchecks() as $feed => $suggestions) {
?>
        <div class="row">
          <div class="col-md-6">
            <h4><?php echo $feed ?></h4>
            <ul class="list-unstyled">
<?php
    foreach ($suggestions as $suggest) {
?>
              <li>
                <h3>Did you mean...</h3>
                <p><a href="<?php echo $suggest->get_link() ?>"><?php echo $suggest->get_formatted_text() ?></a></p>
              </li>
<?php } ?>
            </ul>
          </div>
        </div>
<?php } ?>
      </div>
    </div>
    <!-- ####################### Error ########################### -->
<?php } elseif ($helper->in_error()) { // no spellcheck... is there any error? ?>
    <div class="row">
      <div class="col-md-9">
        <div class="row">
          <div class="col-md-1"></div>
          <div class="col-md-4">
            <h2>Really bad error occured</h2>
          </div>
        </div>
        <div class="row">
          <div class="col-md-10">
            <h2><?php echo $helper->get_error_msg() ?></h2>
          </div>
        </div>
      </div>
    </div>
<?php } ?>

    <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>

