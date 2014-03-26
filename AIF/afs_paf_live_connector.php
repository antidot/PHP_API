<?php
/**
 * Created by PhpStorm.
 * User: agaillard
 * Date: 3/25/14
 * Time: 5:24 PM
 */
require_once("AIF/afs_bows_connector.php");
require_once("AIF/afs_document.php");
require_once("AIF/afs_bows_connector_interface.php");
require_once('AIF/afs_user_authentication.php');
require_once('COMMON/afs_service_status.php');
require_once('AIF/afs_bows_information_cache.php');
require_once('AIF/afs_multipart_response.php');

class AfsPafLiveConnector extends AfsBOWSConnector implements AfsBOWSConnectorInterface
{
    private $paf_name;
    private $authentication;

    /** @brief Construct new PaF connector.
     *
     * @param $host [in] server hosting the Antidot Back Office.
     * @param $service [in] antidot service (see @a AfsService).
     * @param $paf_name [in] name of the PaF.
     * @param $authentication [in-out] authentication object (see
     *        @a AfsAuthentication).
     * @param $scheme [in] Scheme for the connection URL see
     *        @ref uri_scheme (default: @a AFS_SCHEME_HTTP).
     *
     * @exception InvalidArgumentException invalid scheme parameter provided.
     */
    public function __construct($host, AfsService $service, $paf_name,
        AfsAuthentication $authentication, $scheme=AFS_SCHEME_HTTP)
    {
        parent::__construct($host, $service, $scheme);
        $this->paf_name = $paf_name;
        $this->authentication = $authentication;
    }

    /** @brief Retrieves URL using additional parameters.
     * @param $context [in] Query context.
     * @return Valid URL which can be queried using CURL.
     */
    public function get_url($context = null)
    {
        $url = parent::get_base_url('service');

        $params = $this->authentication->format_as_url_param($context['version']);
        $params['afs:layers'] = $context['layers'];

        return sprintf($url . '/%d/instance/%s/paf/%s/process?%s',
            $this->service->id, $this->service->status, $this->paf_name,
            $this->format_parameters($params));
    }

    /** @brief Retrieves authentication as HTTP header for new authentication policy (>=v7.7)
     * @param $context [in] Query context.
     * @return Appropriate HTTP header.
     */
    public function get_http_headers($context=null)
    {
        return $this->authentication->format_as_header_param($context['version']);
    }


    /** @brief Assigns new data to be sent through CURL request.
     *
     * @param $request [in] Correctly initialized CURL request.
     * @param $context [in] Query context.
     */
    public function set_post_content(&$request, $context)
    {
        $doc = $context['document'];
        $document = '@' . $doc->get_filename() . ';type='
            . $doc->get_mime_type();
        if (curl_setopt($request, CURLOPT_POSTFIELDS, array($document)) === false) {
            throw new Exception('Cannot set documents to be sent');
        }
    }

    private function get_bo_version()
    {
        return AfsBOWSInformationCache::get_information($this->host,
            $this->scheme)->get_gen_version();
    }

    public function process_doc($doc, $layers = array("CONTENTS"))
    {
        $context['layers'] = implode(',', $layers);
        $context['version'] = $this->get_bo_version();
        $context['document'] = $doc;
        $res = $this->query($context);
        //Get multipart separator and split response
        $sep = strtok($res, "\r");
        $parts = explode($sep, $res);
        //Response is multipart
        if (count($parts) > 2) {
            //Remove first and last element as they do not contain anything useful
            array_shift($parts);
            array_pop($parts);
        }
        $multipart = new AfsMultipartResponse($parts);
        if (is_array($layers))
            return $multipart->get_layers();
        else
            return $multipart->get_layers()[$layers];
    }
}