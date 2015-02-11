<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/9/15
 * Time: 1:55 PM
 */

require_once 'AFS/afs_query_parameter.php';

class AfsSingleValueParameter extends AfsQueryParameter
{
    protected $key;
    protected $value;

    public function __construct($key, $value) {
        parent::__construct($key);
        $this->value = $value;
    }

    /**
     * @brief set the value of current parameter
     * @param $value
     */
    public function set_value($value) {
        $this->value = $value;
    }

    /**
     * @brief get the current value of parameter
     * @return parameter value
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * @brief format this parameter
     * @return parameter value
     */
    public function format() {
        return $this->value;
    }
}