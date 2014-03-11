<?php
require_once 'AFS/SEARCH/FILTER/afs_combinable_filter.php';


function group($elements)
{
    return new AfsGroupFilter($elements);
}


class AfsGroupFilter extends AfsCombinableFilter
{
    private $elements = null;
    private $previous = null;


    public function __construct($elements, $previous=null)
    {
        $this->elements = $elements;
        $this->previous = $previous;
    }

    public function to_string($current=false)
    {
        if ($current || is_null($this->previous))
            return '(' . $this->elements->to_string() . ')';
        else
            return $this->previous->to_string();
    }
}
