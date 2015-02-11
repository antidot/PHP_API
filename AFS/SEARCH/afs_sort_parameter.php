<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/9/15
 * Time: 4:48 PM
 */

require_once 'AFS/SEARCH/afs_sort_order.php';

class AfsSortParameter  extends AfsSingleValueParameter {

    public function __construct($key, $sort, $feed=null) {
        parent::__construct($key, $sort, $feed);
        $this->sort_order = $sort;
    }

    public function get_sort_order() {
        return $this->value;
    }

    public function format () {
        return array($this->key => $this->value);
    }
}