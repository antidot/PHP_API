<?php
/** @file acp_helper_example.php
 * @example acp_helper_example.php
 */

require_once "PHP_API/afs_lib.php";

$acp = new AfsAcp('eval.partners.antidot.net', 48000);
$acp->query('Men');
$helper = $acp->execute(AfsHelperFormat::HELPERS);

?>


<html>
  <head>
    <title>Antidot PHP API - ACP helper example</title>
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
    <div class="page-header">
      <h1>ACP helper example <small>based on the Antidot PHP API</small></h1>
    </div>

<?php
if ($helper->has_replyset()) {
    echo '
    <div class="row">';
    foreach ($helper->get_replysets() as $suggests) {
        echo '
          <div class="col-md-2">
            <h4><span class="label label-danger">' . $suggests->get_feed() . '</span></h4>
            <ul>';
        
        foreach ($suggests->get_replies() as $suggest) {
            echo '
              <li><ul><span class="label label-info">' . $suggest->get_value() . '</span>';
            foreach ($suggest->get_options() as $key => $value) {
                echo '
                <li>' . $key . ': ' . $value . '</li>';
            }
            echo '
              </ul></li>';
        }
        echo '
            </ul>
          </div>';
    }
    echo '
      </div>
    </div>';
} else {
    echo '
    <div class="row">
      <div class="col-md-1"></div>
      <div class="col-md-5">
        <h2>No suggestion available for <strong>' . $helper->get_query_string() . '</strong></h2>
      </div>
    </div>';
} ?>

    <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>

