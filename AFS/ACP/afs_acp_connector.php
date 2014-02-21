<?php

require_once 'AFS/afs_connector.php';


/** @brief AFS ACP connector.
 *
 * AFS auto complete connnector. */
class AfsAcpConnector extends AfsConnector
{
    public function __construct($host, AfsService $service, $scheme=AFS_SCHEME_HTTP)
    {
        parent::__construct($host, $service, $scheme);
        if ($scheme != AFS_SCHEME_HTTP)
            throw InvalidArgumentException('ACP connector support only HTTP connection');
    }

    /** @brief Retrieves web service name.
     * @return always return 'acp';
     */
    protected function get_web_service_name()
    {
        return 'acp';
    }

    /** @brief Sends an ACP query.
     *
     * Query is built using provided @a parameters.
     * @param $parameters [in] list of parameters used to build the query.
     * @return JSON decoded reply of the query.
     */
    public function send(array $parameters)
    {
        # TODO
    }
}
