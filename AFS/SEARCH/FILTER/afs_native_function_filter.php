<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 11/4/14
 * Time: 8:21 AM
 */

function native_function_filter($function_name, $function_params) {
    return new AfsFilterWrapper(null, new AfsNativeFunctionFilter($function_name, $function_params));
}

class AfsNativeFunctionFilter extends AfsFilter {
    private $function_name = null;
    private $function_params = array();

    public function __construct($function_name, $function_params) {
        $this->function_name = $function_name;
        $this->function_params = $function_params;
    }

    public function to_string() {
        return $this->function_name . '(' . implode(',', $this->function_params) . ')';
    }
}

abstract class AfsNativeFunction {

    const Geo_dist = "geo:dist";
}