<?php

class AfsFeed {
	protected $parameters;
    protected $filters = array();
    protected $sort = array();
	protected $name;
	protected $activated;

	public function __construct($name, $activated, array $parameters=null) {
        $this->activated = $activated;
		$this->name = $name;
		is_null($parameters) ? 	$this->parameters = array() : 
								$this->parameters = $parameters;
	}

    /**
     * @return feed name
     */
	public function get_name() {
		return $this->name;	
	}

    /**
     * @return true if feed is activated for this request
     *          (eg; will be generate a afs:feed parameter in query string)
     */
    public function is_activated() {
        return $this->activated;
    }

    /**
     * @brief to set this feed activated for query (eg. afs:feed parameter in query string)
     * @param $activated
     */
    public function set_activated($activated) {
        $this->activated = $activated;
    }

    /**
     * @brief add a new query parameter to applied on current feed
     * @param $params
     */
	public function add_parameters($params){
		if (!is_array($params)) {
			$params = array($params);
		}
		
		$this->parameters = array_merge($this->parameters, $params);
	}

    /**
     * @brief retrieve a parameter from is key
     * @param $key the key of parameter to get
     * @return the parameter (AfsQueryParameter)if exists, otherwise null
     */
    public function get_parameter($key) {
        foreach ($this->parameters as $parameter) {
            if ($parameter->get_key() === $key) {
                return $parameter;
            }
        }

        return null;
    }

    /**
     * @brief add sort parameter on current feed
     * @param $sort_name
     * @param $order
     */
    public function add_sort($sort_name, $order) {
        $this->sort[] = new AfsSortParameter($sort_name, $order);
    }

    /**
     * @brief check if sort_nmae is set on this feed
     * @param $sort_name
     * @return true if @a $sort_name is set, otherwise false
     */
    public function has_sort($sort_name) {
        foreach ($this->sort as $sort) {
            if ($sort->get_key() === $sort_name) {
                return true;
            }
        }
        return false;
    }

    /**
     * @brief get formated sort string (eg. facetId,sortOrder;facetId1,sortOrder; ...)
     * @return string
     */
    public function get_sort() {
        $result = array();
        foreach ($this->sort as $sort) {
            $result[] = $sort->format();
        }
        return implode(';', $result);
    }

    /**
     * @brief retrieve sort parameter list
     * @return array
     */
    public function get_sorts() {
        return $this->sort;
    }

    /**
     * @brief set a new sort parameter list
     * @param array $sort
     */
    public function set_sort(array $sort) {
        $this->sort = $sort;
    }

    /**
     * @brief check if a filter is set on this feed
     * @param $facet_id
     * @param $value
     * @return true is filter is set, false otherwise
     */
    public function has_filter($facet_id, $value) {
        foreach ($this->filters as $filter) {
            if ($filter->get_facet_id() === $facet_id && in_array($value, $filter->get_values())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @ add a new filter on this feed
     * @param $facet_id
     * @param $values
     */
    public function add_filter($facet_id, $values) {
        if (! is_array($values))
            $values = array($values);

        $filter_found = false;
        foreach($this->filters as $filter) {
            if ($filter->get_facet_id() === $facet_id) {
                $filter_found = true;
                foreach ($values as $value) {
                    if (! in_array($value, $filter->get_values())) {
                        $filter->add_values($value);
                    }
                }
                break;
            }

        }

        if (! $filter_found) {
            $this->filters[] = new AfsFilterParameter($facet_id, $values);
        }
    }

    /**
     * @brief remove a filter already set on this feed
     * @param $facet_id
     * @param $value
     */
    public function remove_filter($facet_id, $value) {
        foreach ($this->filters as $filter) {
            if ($filter->get_facet_id() === $facet_id) {
                $values = $filter->get_values();
                if (($pos_value = array_search($value, $values)) !== false) {
                    unset($values[$pos_value]);
                    if (empty($values)) {
                        $pos_filter = array_search($filter, $this->filters);
                        unset($this->filters[$pos_filter]);
                    } else {
                        $filter->set_values($values);
                    }
                }
                break;
            }
        }

    }

    /**
     * @param $facet_id
     * @throws AfsFilterException
     */
    public function get_filter_values($facet_id) {
        foreach ($this->filters as $filter) {
            if ($filter->get_facet_id() === $facet_id) {
                return $filter->get_values();
            }
        }
        throw new AfsFilterException("$facet_id doesn't exist");
    }

    /**
     * @param $facet_id
     * @param $values
     */
    public function set_filter($facet_id, $values) {
        if (! is_array($values))
            $values = array($values);

        $this->filters = array(new AfsFilterParameter($facet_id, $values));
    }

    /**
     * @return array
     */
    public function get_filters() {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function get_facet_ids() {
        $result = array();
        foreach ($this->filters as $filter) {
            $result[] = $filter->get_facet_id();
        }
        return $result;
    }

    /**
     * @param array $filters
     */
    public function set_filters(array $filters) {
        $this->filters = $filters;
    }

    /**
     * @return mixed
     */
	public function format() {
		return $this->name;
	}

    /**
     * @return array
     */
    private function get_filter_parameters() {
        $result = array();
        foreach ($this->filters as $filter) {
            $result['filter@' . $this->name][$filter->get_facet_id()] =  $filter->get_values();
        }
        return $result;
    }

    /**
     * @param array $parameter_list
     * @return array
     */
    public function get_parameters(array $parameter_list) {
        $result = array();
        foreach ($this->parameters as $parameter) {
            if (in_array($parameter->get_key(), $parameter_list)) {
                if (is_callable(array($parameter, 'get_value'))) {
                    $result[$parameter->get_key() . '@' . $this->name] = $parameter->get_value();
                } elseif (is_callable(array($parameter, 'get_values'))) {
                    $result[$parameter->get_key() . '@' . $this->name] = $parameter->get_values();
                }
            }
        }

        $result = array_merge($result, $this->get_relevent_parameters());

        return array_merge($result, $this->get_filter_parameters());
    }

    private function get_relevent_parameters() {
        $page = $this->get_parameter('page');
        if (! is_null($page) && $page->get_value() !== 1) {
            return array('page@' . $this->name => $page->get_value());
        }
        return array();
    }
}