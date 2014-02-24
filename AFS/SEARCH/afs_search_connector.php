<?php
require_once 'AFS/afs_connector.php';

/** @brief AFS search connector.
 *
 * Only one object of this type should be instanciated in each PHP integration.
 */
class AfsSearchConnector extends AfsConnector
{
    /** @brief Constructs new search connector.
     *
     * All parameter values should have been provided by Antidot.
     *
     * @param $host [in] server hosting the required service.
     * @param $service [in] Antidot service (see @a AfsService).
     * @param $scheme [in] Scheme for the connection URL see
     *        @ref uri_scheme (default: @a AFS_SCHEME_HTTP).
     *
     * @exception InvalidArgumentException invalid scheme parameter provided.
     */
    public function __construct($host, AfsService $service, $scheme=AFS_SCHEME_HTTP)
    {
        parent::__construct($host, $service, $scheme);
        if ($scheme != AFS_SCHEME_HTTP)
            throw InvalidArgumentException('Search connector support only HTTTP connection');
    }

    /** @brief Retrieves web service name.
     * @return always return 'search';
     */
    protected function get_web_service_name()
    {
        return 'search';
    }
}


