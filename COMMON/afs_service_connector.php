<?php
require_once 'COMMON/afs_connector_base.php';
require_once 'COMMON/afs_service.php';


/** @brief Base class for AFS connectors which require AFS service. */
abstract class AfsServiceConnector extends AfsConnectorBase
{
    protected $service = null;

    /** @brief Constructs new base connector managing AFS service.
     *
     * All parameter values should have been provided by Antidot.
     *
     * @param $host [in] server hosting the required service.
     * @param $service [in] Antidot service (see @a AfsService).
     * @param $scheme [in] Scheme for the connection URL see
     *        @ref uri_scheme.
     *
     * @exception InvalidArgumentException invalid scheme parameter provided.
     */
    protected function __construct($host, AfsService $service, $scheme)
    {
        parent::__construct($host, $scheme);
        $this->service = $service;
    }
}


