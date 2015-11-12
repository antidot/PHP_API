<?php

/**
 * Created by PhpStorm.
 * User: ct
 * Date: 11/11/15
 * Time: 10:38 AM
 */
class AfsMetadataHelper {

    private $facetsInfo = array();

    public function __construct($data) {
        $this->initialize_facets_info($data);
    }

    private function initialize_facets_info($meta) {
        $this->initialize_facets_info_rec($meta->meta->info->searchFeedInfo->setInfos);
    }

    private function initialize_facets_info_rec($setInfos) {
        foreach ($setInfos as $setInfo) {
            foreach ($setInfo->facetInfos as $facetInfo) {
                $this->facetsInfo[$facetInfo->id] = new AfsFacetInfoHelper($facetInfo);
            }
            if (property_exists($setInfo, 'childrenInfos')) {
                $this->initialize_facets_info_rec($setInfo->childrenInfos);
            }
        }
    }

    /**
     * @brief retreives only facets info
     * @return array of AfsFacetInfo objects
     */
    public function get_facets_info() {
        return array_filter($this->facetsInfo, function($facet_info) {
            return ! $facet_info->is_filter();
        });
    }

    /**
     * @brief retreives only filters info
     * @return array of AfsFacetInfo objects
     */
    public function get_filters_info() {
        return array_filter($this->facetsInfo, function($facet_info) {
            return $facet_info->is_filter();
        });
    }

    /**
     * @brief retrieves all facets and filters info
     * @return array of AfsFacetInfo objects
     */
    public function get_facets_and_filters_info() {
        return $this->facetsInfo;
    }
}



class AfsFacetInfoHelper {

    private $labels = array();
    private $is_sticky = null;
    private $is_filter = null;
    private $type = null;
    private $layout = null;
    private $id = null;

    public function __construct($facetInfo)  {
        $this->type = $facetInfo->type;
        if (property_exists($facetInfo, 'labels')) {
            foreach ($facetInfo->labels as $label) {
                if (property_exists($label, 'lang')) {
                    $this->labels[$label->lang] = $label->label;
                } elseif (property_exists($label, 'label')) {
                    $this->labels[$label->label] = $label->label;
                }
            }
        }

        if (property_exists($facetInfo, 'filter')) {
            $this->is_filter = $facetInfo->filter;
        }
        if (property_exists($facetInfo, 'sticky')) {
            $this->is_sticky = $facetInfo->sticky;
        }
        $this->id = $facetInfo->id;
        $this->layout = $facetInfo->layout;
    }

    /**
     * @brief retrieves all facet's labels
     * @return array of labels of language => label
     */
    public function get_labels() {
        return $this->labels;
    }

    /**
     * @return boolean: true if current facet is sticky
     */
    public function is_sticky() {
        return $this->is_sticky;
    }

    /**
     * @return boolean: true if current facet is a filter, otherwise false
     */
    public function is_filter() {
        return $this->is_filter;
    }

    /**
     * @param $value
     */
    public function set_is_filter($value) {
        $this->is_filter = $value;
    }

    /**
     * @return integer: current facet's ID
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @return string: current facet's type
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * @return string: current facet's layout
     */
    public function get_layout() {
        return $this->layout;
    }
}
