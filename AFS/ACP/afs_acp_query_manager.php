<?php
require_once 'AFS/ACP/afs_acp_configuration.php';

/** @brief Manages ACP queries.
 */
class AfsAcpQueryManager
{
    private $connector = null;
    private $config = null;


    /** @brief Constructs new ACP query manager.
     *
     * @param $connector [in] Connector used to submit a query to AFS ACP engine.
     * @param $config [in] ACP main configuration (see AfsAcpConfiguration for
     *        more details). Default instance is constructed when no
     *        configuration is provided.
     */
    public function __construct(AfsConnectorInterface $connector,
        AfsAcpConfiguration $config=null)
    {
        $this->connector = $connector;
        $this->config = (is_null($config) ? new AfsAcpConfiguration() : $config);
    }

    /** @brief Send query to AFS ACP engine.
     * @param $query [in] @a AfsAcpQuery object to use in order to generate
     *        appropriate ACP query.
     * @return reply of AFS ACP engine.
     */
    public function send(AfsAcpQuery $query, $text_encoding=TextEncoding::UTF8)
    {
        $query->initialize_user_and_session_id($this->config->get_user_session_manager());
        $params = $this->convert_to_param($query);
        return $this->connector->send($params, $text_encoding);
    }

    /** @internal
     * @brief Converts @a AfsAcpQuery to arrays of parameters.
     *
     * Retrieves parameters from query and builds appropriate parameters to be
     * sent to AFS ACP engine.
     *
     * @param $query [in] AfsAcpQuery to transform.
     *
     * @return array of key-value pairs where key is a valid AFS ACP query
     * parameter name and the associated value is a valid string for this
     * specific parameter name.
     */
    private function convert_to_param(AfsAcpQuery $query)
    {
        $params = array();
        foreach ($query->get_parameters() as $param => $values) {
            $params['afs:' . $param] = $values;
        }
        return $params;
    }
}
