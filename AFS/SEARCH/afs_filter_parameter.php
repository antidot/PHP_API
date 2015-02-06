<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/6/15
 * Time: 3:49 PM
 */

class AfsFilterParameter  extends AfsMultipleValuesParameter {
	protected $facet_id;

    public function __construct($facetId, $values, $feed=null) {
        parent::__construct('filter', $values, $feed);
        $this->facet_id = $facetId;
    }

    public function get_facet_id() {
        return $this->facet_id;
    }

    public function format () {
        return array($this->facet_id => $this->values);
    }
} 