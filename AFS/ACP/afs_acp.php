<?php
require_once 'COMMON/afs_service.php';
require_once 'AFS/ACP/afs_acp_query.php';
require_once 'AFS/ACP/afs_acp_query_manager.php';
require_once 'AFS/ACP/afs_acp_connector.php';
require_once 'AFS/ACP/afs_acp_configuration.php';
require_once 'AFS/ACP/afs_acp_response_helper.php';

/** @brief Facade for AFS ACP engine query. */
class AfsAcp
{
    private $service = null;
    private $connector = null;
    private $config = null;
    private $query = null;


    /** @brief Constructs AFS ACP facade.
     *
     * @param $host [in] server hosting the required service.
     * @param $id [in] identifier of the desired service.
     * @param $status [in] status of the desired service (see @ref AfsServiceStatus).
     */
    public function __construct($host, $id, $status=AfsServiceStatus::STABLE)
    {
        $this->service = new AfsService($id, $status);
        $this->connector = new AfsAcpConnector($host, $this->service);
        $this->config = new AfsAcpConfiguration();
        $this->query = new AfsAcpQuery();
    }

    /**
     * @Brief set the user encoding for URL building and response parsing
     *        default used is UTF-8. UTF-8 and ISO-8859-1 are supported
     * @param TextEncoding [in] $encoding
     */
    public function set_text_encoding($encoding) {
        $this->config->set_text_encoding($encoding);
    }

    /**
     * @Brief get the actual user encoding
     * @return the current encoding (UTF-8 or ISO-8859-1)
     */
    public function get_text_encoding() {
        return $this->config->get_text_encoding();
    }

    /** @name Query management
     *
     * Remember that AfsAcpQuery objects are immutable.
     * @{ */

    /** @brief Defines query string for ACP engine.
     *
     * This is a shortcut to:
     * @code $query = $acp->get_query();
       $query = $query->set_query($value)->set_feed('value1')->add_feed('value2');
       $acp->set_query($query); @endcode
     * This can be writtent in onle line as following:
     * @code $acp->query($value, array('value1', 'value2')); @endcode
     *
     * @param $value [in] New value to submit to ACP engine.
     * @param $feeds [in] List of feeds to filter on. By default, there is no
     *        filter on feeds (empty array).
     */
    public function query($value, $feeds=array())
    {
        $this->query = $this->query->set_query($value);
        if (! empty($feeds)) {
            $this->query = $this->query->set_feed(array_shift($feeds));
            foreach ($feeds as $feed)
                $this->query = $this->query->add_feed($feed);
        }
    }
    /** @brief Retrieves current query.
     * @return AFS ACP query.
     */
    public function get_query()
    {
        return $this->query;
    }
    /** @brief Defines new query.
     * @param $query [in] New query to set.
     */
    public function set_query(AfsAcpQuery $query)
    {
        $this->query = $query;
    }

    /** @brief Executes query.
     * @param $format [in] prefered result format.
     * @return Helper or array depending on chosen $format.
     */
    public function execute($format=AfsHelperFormat::ARRAYS)
    {
        $this->config->set_helper_format($format);
        $query_mgr = new AfsAcpQueryManager($this->connector, $this->config);
        $reply = $query_mgr->send($this->query, $this->config->get_text_encoding());
        $helper = new AfsAcpResponseHelper($reply, $this->config);
        if (AfsHelperFormat::ARRAYS == $format)
            return $helper->format();
        else
            return $helper;
    }

    /** @brief Retrieves URL used to query AFS ACP engine.
     *
     * Useful for debug purpose only. It should be called after
     * AfsAcp::execute has been called.
     * @return generated URL for AFS ACP engine.
     */
    public function get_generated_url()
    {
        return $this->connector->get_generated_url();
    }
    /** @} */

    /** @name Miscellaneous accessors
     * @{ */

    /** @brief Retrieves current AFS service.
     * @return AFS service.
     */
    public function get_service()
    {
        return $this->service;
    }

    /** @brief Retrieves helper configuration.
     * @return AFS Helper configuration.
     */
    public function get_helpers_configuration()
    {
        return $this->config;
    }

    /** @brief Retrieves ACP engine connector.
     * @return AFS ACP connector.
     */
    public function get_connector()
    {
        return $this->connector;
    }
    /** @} */
}
