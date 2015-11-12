<?php

/**
 * Created by PhpStorm.
 * User: ct
 * Date: 11/10/15
 * Time: 5:00 PM
 */
class AfsIntrospection
{
    private $search = null;

    public function __construct($search)
    {
        $this->search = $search;
    }

    public function get_metadata($feed_name=null)
    {
        $params = array('afs:what' => 'meta');
        if (! is_null($feed_name)) {
            $params['feed'] = $feed_name;
        }
        $query = AfsQuery::create_from_parameters($params);
        $result = $this->search->execute($query);
        return $result->get_metadata($feed_name);
    }
}
