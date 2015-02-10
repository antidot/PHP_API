<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/6/15
 * Time: 11:33 AM
 */

class AfsMultipleValuesParameter extends AfsQueryParameter
{
    protected $key;
    protected $values;

    public function __construct($key, array $values) {
        parent::__construct($key);
        $this->values = $values;
    }

    /**
     * @brief add a value on current parameter
     * @param $values
     */
    public function add_values($values) {
        if (is_array($values))
            $this->values = array_merge($this->values, $values);
        else
            $this->values[] = $values;
    }

    /**
     * @brief remove a value on current parameter
     * @param $value
     * @return true is there is more values on current parameter, false otherwise
     */
    public function remove_value($value) {
    	if (($key = array_search($value, $this->values)) !== false) {
    		unset($this->values[$key]);
    		if (empty($this->values))
    			return false;
    		else
    			return true;
    	}
    	return true;
    }

    /**
     * @brief set a new list of values on current parameter
     * @param array $values
     */
    public function set_values(array $values) {
    	$this->values = $values;
    }

    /**
     * @brief get all values setted on current parameter
     * @return array of values
     */
    public function get_values()
    {
        return $this->values;
    }

    /**
     * @brief format this parameter
     * @return array|void
     */
    public function format() {
        return array ($this->key, $this->values);

    }
} 