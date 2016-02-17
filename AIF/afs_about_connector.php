<?php
require_once 'AIF/afs_bows_connector.php';
require_once 'AIF/afs_bows_connector_interface.php';
require_once 'AIF/afs_bows_information.php';
require_once 'AIF/afs_token_authentication.php';


/** @brief Simple connector to retrieve AFS Back Office information. */
class AfsAboutConnector extends AfsBOWSConnector implements AfsBOWSConnectorInterface
{
    public function __construct($host, AfsService $service=null, $scheme=AFS_SCHEME_HTTP, SAI_CurlInterface $curlConnector=null)
    {
        parent::__construct($host, $service, $scheme, $curlConnector);
    }

    /** @brief Retrieves URL.
     * @param $context [in] Unused parameter.
     * @return Valid URL to query information about installed AFS Back Office.
     */
    public function get_url($context=null)
    {
        return parent::get_base_url('about');
    }

    /** @brief Retrieves AFS Back Office Web Services information.
     *
     * This trigger a query to the host hosting AFS Back Office.
     *
     * @return AfsBOWSInformation object.
     */
    public function get_information()
    {
        return new AfsBOWSInformation(json_decode($this->query()));
    }
}
