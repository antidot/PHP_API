<?php
require_once 'AFS/SEARCH/afs_query_coder_interface.php';
require_once 'AFS/SEARCH/afs_filter_coder.php';
require_once 'AFS/SEARCH/afs_feed_coder.php';
require_once 'AFS/SEARCH/afs_sort_coder.php';
require_once 'AFS/SEARCH/afs_query.php';

/** @brief Default query coder implementation. */
class AfsQueryCoder implements AfsQueryCoderInterface
{
    private $path = null;
    private $feed_coder = null;
    private $filter_coder = null;
    private $sort_coder = null;

    /** @brief Construct new instance.
     * @param $path [in] base path used to generate appropriate link (see
     *        @a generate_link method). If this parameter is not provided, value
     *        of $_SERVER['PHP_SELF'] is used as default value.
     * @param $feed_coder [in] (optional) feed coder. If not set, default
     *        implementation is used (see @a AfsFeedCoder).
     * @param $filter_coder [in] (optional) filter coder. If not set, default
     *        implementation is used (see @a AfsFilterCoder).
     * @param $sort_coder [in] (optional) sort parameters coder. If not set,
     *        default implementation is used (see @a AfsSortCoder).
     */
    public function __construct($path=null, AfsCoderInterface $feed_coder=null,
        AfsCoderInterface $filter_coder=null, AfsCoderInterface $sort_coder=null)
    {
        if (is_null($path))
            $path = $_SERVER['PHP_SELF'];
        if (is_null($feed_coder))
            $feed_coder = new AfsFeedCoder();
        if (is_null($filter_coder))
            $filter_coder = new AfsFilterCoder();
        if (is_null($sort_coder))
            $sort_coder = new AfsSortCoder();
        $this->path = $path;
        $this->feed_coder = $feed_coder;
        $this->filter_coder = $filter_coder;
        $this->sort_coder = $sort_coder;
    }

    /** @brief Generate URL parameters from @a AfsQuery.
     * @param $query [in] query to transform to URL parameters.
     * @return appropriate URL parameters representing input @a query.
     */
    public function generate_parameters(AfsQuery $query)
    {
        $result = array();
        foreach ($query->get_parameters(false) as $param => $values) {
            $result[] = $param . '=' . htmlspecialchars(urlencode(
                $this->coder($param, $values, 'encode')));
        }
        return implode('&', $result);
    }

    /** @brief Convenient method to build link.
     *
     * Combine @a path parameter to result of @a generate_parameters call.
     *
     * @param $query [in] @a AfsQuery used to generate appropriate link.
     * @return link which can be directly used to query AFS search engine.
     */
    public function generate_link(AfsQuery $query)
    {
        return $this->path . '?' . $this->generate_parameters($query);
    }

    /** @brief Generate query from URL parameters.
     * @param $params [in] array of parameters. Usually set to $_GET.
     * @return @a AfsQuery correctly initialized.
     */
    public function build_query(array $params)
    {
        $buffer = array();
        foreach ($params as $param => $values) {
            $buffer[$param] = $this->coder($param, $values, 'decode');
        }
        $query = AfsQuery::create_from_parameters($buffer);
        return $query;
    }

    /** @internal
     * @brief Encode/decode provided values.
     *
     * If necessary coder exists, it is used to encode/decode provided
     * @a values. Otherwise, @a values parameter is returned as is.
     *
     * @param $param_name [in] name of parameter to work on.
     * @param $values [in] one or multiple values to encode/decode.
     * @param $action [in] @c encode or @c decode (see interface
     *        @a AfsCoderInterface).
     *
     * @return encode/decode or unmodified values.
     */
    private function coder($param_name, $values, $action)
    {
        $coder = $param_name . '_coder';
        if (property_exists($this, $coder)) {
            return $this->$coder->$action($values);
        } else {
            return $values;
        }
    }
}


