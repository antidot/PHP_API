<?php
/** @file afs_connector_base.php */
require_once 'COMMON/afs_service.php';
require_once 'COMMON/php-SAI/lib/Curl.php';

/** @defgroup uri_scheme Connection scheme
 *
 * Antidot Web Services can be queried in standard HTTP mode or in secured mode
 * (HTTPS).
 * @{ */

/** @brief HTTP: Non secured mode */
define('AFS_SCHEME_HTTP', 'http');
/** @brief HTTPS: Secured mode */
define('AFS_SCHEME_HTTPS', 'https');
/** @} */


/** @brief Base class for AFS connectors.
 *
 * This class provided usefull methods to manage connection strings. */
abstract class AfsConnectorBase
{
    protected $scheme = null;
    protected $host = null;
    protected $service = null;
    protected $curlConnector = null;

    /** @brief Constructs new base connector.
     *
     * All parameter values should have been provided by Antidot.
     *
     * @param $host [in] Server hosting the required service.
     * @param $service [in] Antidot service (see @a AfsService).
     * @param $scheme [in] Scheme for the connection URL see
     *        @ref uri_scheme.
     * @param $curlConnector [in] Connector to curl, useful for mocking curl calls
     *
     * @exception InvalidArgumentException invalid scheme parameter provided.
     */
    protected function __construct($host, AfsService $service=null, $scheme=null, SAI_CurlInterface $curlConnector=null)
    {
        if ($scheme != AFS_SCHEME_HTTP && $scheme != AFS_SCHEME_HTTPS)
            throw InvalidArgumentException('Connector supports only HTTP and HTTPS connections');

        $this->scheme = $scheme;
        $this->host = $host;
        $this->service = $service;
        // If no custom connector was provided we set it to use real curl
        if (is_null($curlConnector)) {
            $this->curlConnector = new SAI_Curl();
        } else {
            $this->curlConnector = $curlConnector;
        }
    }


    protected function format_parameters(array $parameters)
    {
        $string_parameters = array();
        foreach ($parameters as $name => $values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    $string_parameters[] = $this->encode_param($name, $value);
                }
            } else {
                $string_parameters[] = $this->encode_param($name, $values);
            }
        }
        return implode('&', $string_parameters);
    }

    private function encode_param($key, $value)
    {
        return urlencode($key) . '=' . urlencode($value);
    }

    protected function post_add_opts(&$opts) {
        if (strpos(phpversion(), '5.6') !== false) {
            $opts[CURLOPT_SAFE_UPLOAD] = false;
        }
    }
}


