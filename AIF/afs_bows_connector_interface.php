<?php


/** @brief Interface to query AFS Back Officce Web Services.
 *
 * This use CURL to achieve its job.
 */
interface AfsBOWSConnectorInterface
{
    /** @brief Retrieves additional HTTP headers to set.
     * @param $context [in] Query context.
     * @return HTTP headers as array of key-value pairs.
     */
    public function get_http_headers($context=null);

    /** @brief Retrieves URL using additional parameters.
     * @param $context [in] Query context.
     * @return Valid URL which can be queried using CURL.
     */
    public function get_url($context=null);

    /** @brief Assigns new data to be sent through CURL request.
     *
     * @param $request [in] Correctly initialized CURL request.
     * @param $context [in] Query context.
     */
    public function set_post_content(&$request, $context);
}
