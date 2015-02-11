<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/9/15
 * Time: 1:56 PM
 */
abstract class AfsQueryParameter
{
    protected $key;

    public function __construct($key) {
        $this->key = $key;
    }

    public function get_key() {
        return $this->key;
    }

    public function copy() {
        return clone($this);
    }

    public function format() {
        throw new AfsNotImplementedException();
    }
}