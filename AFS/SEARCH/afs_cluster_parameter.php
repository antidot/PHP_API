<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/9/15
 * Time: 5:47 PM
 */

require_once 'AFS/afs_single_value_parameter.php';

class AfsClusterParameter  extends AfsSingleValueParameter {
    protected $facet_id;

    public function __construct($facetId, $value, $feed=null) {
        parent::__construct('cluster', $value, $feed);
        $this->facet_id = $facetId;
    }

    public function get_facet_id() {
        return $this->facet_id;
    }

    public function format () {
        return $this->facet_id . ',' . $this->value;
    }
}