<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/9/15
 * Time: 1:56 PM
 */
abstract class AfsQueryParameter
{
    protected $feed;
    protected $key;

    public function __construct($key, $feed=null) {
        $this->feed = $feed;
        $this->key = $key;
    }

    public function get_key() {
        return $this->key;
    }

    public function get_feed() {
        return $this->feed;
    }

    public function copy() {
        return clone($this);
    }

    public function format() {
        throw new AfsNotImplementedException();
    }
}

class AfsParameterCollection {
    protected $parameter_list = array();

    public function __construct(AfsParameterCollection $collection) {
        if (! is_null($collection)) {
            $this->parameter_list = $collection->parameter_list;
        }
    }

    public function append(AfsQueryParameter $element) {
        $this->parameter_list[] = $element;
    }

    public function is_empty() {
        return empty($this->parameter_list);
    }

    public function add_or_replace(AfsQueryParameter $element) {
        if (($parameter = $this->get($element->get_Key())) != -1) {
            $this->remove($parameter);
        }

        $this->parameter_list[] = $element;
    }

    public function remove(AfsQueryParameter $element) {
        $pos = array_search($element, $this->parameter_list);
        unset($this->parameter_list[$pos]);
    }

    public function get($key) {
        foreach ($this->parameter_list as $parameter) {
            if ($parameter->get_key() === $key) {
                return $parameter;
            }
        }
        return -1;
    }
}

