<?php

/**
 * Created by PhpStorm.
 * User: ct
 * Date: 11/10/15
 * Time: 5:00 PM
 */
class AfsIntrospection {
    private $feeds_metadata = array();

    public function __construct($search) {
        $this->build_metadata($search);
    }

    /**
     * @param $feed
     * @return bool: true if there is metadata for given feed, otherwise false
     */
    public function has_feed($feed) {
        return array_key_exists($feed, $this->feeds_metadata);
    }

    /**
     * @return array: list of available feed's metadata
     */
    public function get_feeds_list() {
        return array_keys($this->feeds_metadata);
    }

    /**
     * @return array: list of feed's metadata (AfsMetadataHelper)
     */
    public function get_all_metadata() {
        return $this->feeds_metadata;
    }

    /**
     * @param $feed_name
     * @return AfsMetadata: metadata for given feed name, or null if there is no metadata for given feed name
     */
    public function get_feed_metadata($feed_name) {
        if (array_key_exists($feed_name, $this->feeds_metadata)) {
            return $this->feeds_metadata[$feed_name];
        } else {
            return null;
        }
    }

    private function build_metadata($search) {
        $params = array('afs:what' => 'meta');
        $query = AfsQuery::create_from_parameters($params);
        $result = $search->execute($query);
        $this->feeds_metadata = $result->get_all_metadata();
    }

    private function setup_feeds_filters($search) {
        $query = AfsQuery::create_from_parameters(array());
        $result = $search->execute($query);
        if (property_exists($result, 'replysets')) {
            foreach ($this->feeds_metadata as $metadata_feed => $metadata_helper) {
                $this->setup_feed_filters($result->replysets, $metadata_feed, $metadata_helper);
            }
        }
    }
}
