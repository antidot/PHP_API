<?php

/** @brief Interface for encoding/decoding queries.
 *
 * Encoded query is used to generate parameters suitable to build link for
 * specific action.<br/>
 * Query is decoded from array of parameters. This array usually comes from
 * <tt>$_GET</tt> value.
 *
 * For example:
 * - Suppose you are looking for shoes in your favorite web store.
 * - First you search for the word <tt>shoes</tt>.
 * - Then some results are presented along with @c size and @c color facets.
 * - Now you want to filter on @c red color.
 * - You have to build the @c query object from URL parameters, otherwise
 *   if you filter on the color only, you will get all red items. So you should
 *   initialize your query with:
 *   @code $query = $query_coder->build_query($_GET);@endcode
 * - Now you want to filter on red color, you have to add a filter to the query:
 *   @code $query = $query->add_filter('color', 'red'); @endcode
 * - Then you can generate parameters suitable for a valid HTTP link by calling:
 *   @code $params = $query_coder->generate_parameters($query); @endcode.
 */
interface AfsQueryCoderInterface
{
    /** @brief Generate suitable parameters which can be used in URL.
     * @param $query [in] query object to encode.
     * @return encoded query object.
     */
    public function generate_parameters(AfsQuery $query);
    /** @brief Convenient method to build link.
     *
     * This method generally calls @a generate_parameters to build appropriate 
     * link.
     * @param $query [in] @a AfsQuery used to generate appropriate link.
     * @return link which can be directly used to query AFS search engine.
     */
    public function generate_link(AfsQuery $query);
    /** @brief Generate query object from array of parameters.
     * @param $params [in] array of parameters used to build query object.
     * @return properly initialized query object.
     */
    public function build_query(array $params);
}

?>
