<?php
require_once "afs_query_coder_interface.php";
require_once "afs_filter_coder.php";
require_once "afs_feed_coder.php";

/** @brief Default query coder implementation. */
class AfsQueryCoder implements AfsQueryCoderInterface
{
    private $path = null;
    private $feed_coder = null;
    private $filter_coder = null;

    /** @brief Construct new instance.
     * @param $path [in] base path used to generate appropriate link (see
     *        @a generate_link method).
     * @param $feed_coder [in] (optional) feed coder. If not set, default
     *        implementation is used (see @a AfsFeedCoder).
     * @param $filter_coder [in] (optional) filter coder. If not set, default
     *        implementation is used (see @a AfsFilterCoder).
     */
    public function __construct($path, AfsCoderInterface $feed_coder=null,
        AfsCoderInterface $filter_coder=null)
    {
        if ($feed_coder == null) {
            $feed_coder = new AfsFeedCoder();
        }
        if ($filter_coder == null) {
            $filter_coder = new AfsFilterCoder();
        }
        $this->path = $path;
        $this->feed_coder = $feed_coder;
        $this->filter_coder = $filter_coder;
    }

    /** @brief Generate URL parameters from @a AfsQuery.
     * @param $query [in] query to transform to URL parameters.
     * @return appropriate URL parameters representing input @a query.
     */
    public function generate_parameters(AfsQuery $query)
    {
        $result = array();
        foreach ($query->get_parameters() as $param => $values) {
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
        return AfsQuery::create_from_parameters($buffer);
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

?>
