<?php
require_once "COMMON/afs_connector_base.php";

/** @brief AFS Back Office Web Service connector.
 */
abstract class AfsBOWSConnector extends AfsConnectorBase
{
    /** @brief Constructs new Back Office Web Service connector.
     *
     * @param $host [in] server hosting the Antidot Back Office.
     * @param $service [in] Antidot service (see @a AfsService).
     * @param $scheme [in] Scheme for the connection URL see
     *        @ref uri_scheme (default: @a AFS_SCHEME_HTTP).
     *
     * @exception InvalidArgumentException invalid scheme parameter provided.
     */
    public function __construct($host, AfsService $service=null, $scheme=AFS_SCHEME_HTTP)
    {
        parent::__construct($host, $service, $scheme);
    }

    /** @internal
     * @brief Builds base URL including required web service name.
     * @param $service [in] Service name (not AFS service but WS service).
     * @return full URL to query.
     */
    protected function get_base_url($service)
    {
        return sprintf('%s://%s/bo-ws/%s', $this->scheme, $this->host, $service);
    }

    /** @internal
     * @brief Sets curl options.
     *
     * By default, following HTTP headers are always added:
     * - Expect:
     * - Accept: application/json
     *
     * @param $request [in-out] Curl request to update with appropriate options.
     * @param $headers [in] Array of additional HTTP header parameters
     *        (default=null). This array must be defined as key => value.
     *
     * @exception Exception when setting options fails.
     */
    private function set_curl_options(&$request, array $headers=null)
    {
        $default_headers = array('Expect' => '', 'Accept' => 'application/json');
        if (!is_null($headers) && !empty($headers))
            $default_headers = array_merge($default_headers, $headers);

        if (curl_setopt_array($request,
                array(CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_HTTPHEADER => $this->format_headers($default_headers),
                      CURLOPT_SSL_VERIFYPEER => false,
                      CURLOPT_SSL_VERIFYHOST => false
                      )) === false) {
            throw new Exception('Cannot define query options');
        }
    }

    /** @internal
     * @brief Formats array of key-value pairs as HTTP headers for CURL.
     * @param $headers [in] Array of key-value pairs to format.
     * @return array of formatted values.
     */
    private function format_headers(array &$headers)
    {
        $result = array();
        foreach($headers as $key => $value)
            $result[] = $key . ': ' . $value;
        return $result;
    }

    /** @internal
     * @brief Queries appropriate Web Service.
     *
     * @param $context [in] This context is transmitted as is to other
     *        method.
     *
     * @return json_decoded reply of the Web Service.
     *
     * @exception Exception on error.
     */
    protected function query($context=null)
    {
        $url = $this->get_url($context);
        $request = curl_init($url);
        if ($request == false) {
            throw new Exception('Cannot initialize connexion to "' . $this->host
                .'" with URL: ' . $url);
        }
        $this->set_curl_options($request, $this->get_http_headers($context));
        if (!is_null($context))
            $this->set_post_content($request, $context);
        $result = curl_exec($request);
        if ($result === false) {
            throw new Exception('Failed to execute request: ' . $url . ' ['
                . curl_error($request) . ']');
        }
        curl_close($request);

        return $result;
    }

    /** @brief Default implementation: do nothing.
     * @param $context [in] Query context (unused).
     * @return Always return @c null.
     */
    public function get_http_headers($context=null)
    {
        return null;
    }

    /** @brief Default implementation: do nothing.
     *
     * @param $request [in] CURL request (not used).
     * @param $context [in] Query context (unused).
     */
    public function set_post_content(&$request, $context)
    {
    }
}


