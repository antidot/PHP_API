<?php
require_once "afs_connector_interface.php";
require_once "afs_connector_base.php";
require_once "afs_service.php";

/** @brief AFS search connector.
 *
 * Only one object of this type should be instanciated in each PHP integration.
 */
class AfsSearchConnector extends AfsConnectorBase implements AfsConnectorInterface
{
    protected $search_url;
    protected $service;

    /** @brief Construct new search connector.
     *
     * All parameter values should have been provided by Antidot.
     *
     * @param $search_url [in] URL of the AFS search engine.
     * @param $service [in] Antidot service (see @a AfsService).
     */
    public function __construct($search_url, AfsService $service)
    {
        $this->search_url = $search_url;
        $this->service = $service;
    }

    /** @brief Send a query.
     *
     * Query is built using provided @a parameters.
     * @param $parameters [in] list of parameters used to build the query.
     * @return JSON decoded reply of the query.
     */
    public function send(array $parameters)
    {
        $url = $this->build_url($parameters);
        $request = curl_init($url);
        if ($request == false) {
            $result = $this->build_error('Cannot initialize connexion', $url);
        } else {
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_FAILONERROR, true);
            $result = curl_exec($request);
            if ($result == false) {
                $result = $this->build_error('Failed to execute request',  $url);
            }
            curl_close($request);
        }
        return json_decode($result);
    }

    protected function build_url(array $parameters)
    {
        $parameters['afs:service'] = $this->service->id;
        $parameters['afs:status'] = $this->service->status;
        $parameters['afs:output'] = 'json,2';
        return $this->search_url . '?' . $this->format_parameters($parameters);
    }

    private function build_error($message, $details)
    {
        error_log("$message [$details]");
        return '{ "header": { "error": { "message": [ "' . $message . '" ] } } }';
    }
}

?>
