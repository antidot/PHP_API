<?php


/** @brief Interface to query AFS Back Officce Web Services.
 *
 * This use CURL to achieve its job.
 */
interface AfsBOWSConnectorInterface
{
    /** @brief Retrieves additional HTTP headers to set.
     * @return HTTP headers as array of key-value pairs.
     */
    public function get_http_headers();

    /** @brief Retrieves URL using additional parameters.
     * @param $params [in] Additional parameters to used (default=null).
     * @return Valid URL which can be queried using CURL.
     */
    public function get_url(array $params=null);

    /** @brief Assigns new data to be sent through CURL request.
     *
     * @param $request [in] Correctly initialized CURL request.
     * @param $post_data_mgr [in] Data manager which can update the request
     *        object.
     */
    public function set_post_content(&$request, $post_data_mgr);
}
