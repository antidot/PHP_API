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
        $this->build_reply_as_associative_array();
    }

    /** @brief Retrieves web service name.
     * @return always return 'acp';
     */
    protected function get_web_service_name()
    {
        return 'acp';
    }

    /** @internal
     * @brief Overload default implemantation with something easiest to handle 
     * for ACP.
     *
     * @param $message [in] Error message.
     * @param $details [in] Error details.
     *
     * @return Associated array with error and details.
     */
    protected function build_error($message, $details)
    {
        return array('error' => $message, 'details' => $details);
    }

    /** @internal
     * @brief Overloads default implementation by setting json version to 1 in order to make it work with the API. 
     * @param $parameters [in-out] List of parameters to update with standard parameters.
     */
    protected function update_with_defaults(array& $parameters)
    {
        parent::update_with_defaults($parameters);
        #override output version
        $parameters['afs:output'] = 'json,1';
    }
}
