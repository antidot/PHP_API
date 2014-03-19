<?php
require_once 'AIF/afs_authentication.php';
require_once 'AIF/afs_document_manager.php';
require_once 'AIF/afs_paf_upload_reply.php';
require_once 'AIF/afs_about_connector.php';
require_once 'COMMON/afs_service_connector.php';

/** @brief AFS PaF connector.
 */
class AfsPafConnector extends AfsServiceConnector
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

    /** @brief Upload one document to the PaF.
     *
     * @param $doc [in] simple document (see @a AfsDocument).
     * @param $comment [in] comment associated to this action (default=null).
     * @return command result (see @a AfsPafUploadReply).
     *
     * @exception see @a upload_docs method.
     */
    public function upload_doc(AfsDocument $doc, $comment=null)
    {
        $mgr = new AfsDocumentManager();
        $mgr->add_document($doc);
        return $this->upload_docs($mgr, $comment);
    }

    /** @brief Upload one or more documents through document manager.
     *
     * @param $mgr [in] document manager (see @a AfsDocumentManager).
     * @param $comment [in] comment associated to this action (default=null).
     * @return command result (see @a AfsPafUploadReply).
     *
     * @exception InvalidArgumentException when no document is being sent.
     * @exception Exception when error occured while initializing or executing
     *            request.
     */
    public function upload_docs(AfsDocumentManager $mgr, $comment=null)
    {
        if (! $mgr->has_document()) {
            throw new InvalidArgumentException('No document to be sent');
        }

        $version = $this->get_bo_version();
        $url = $this->get_url($comment);
        $url .= '&' . $this->authentication->format_as_url_param($version);

        $request = curl_init($url);
        if ($request == false) {
            throw new Exception('Cannot initialize connexion to send documents');
        }
        $headers = $this->authentication->format_as_header_param($version);
        $this->set_default_curl_options($request, $headers);
        $this->set_documents_to_send($request, $mgr);
        $result = curl_exec($request);
        if ($result === false) {
            throw new Exception('Failed to execute request: ' . $url . ' ['
                . curl_error($request) . ']');
        }
        curl_close($request);

        return new AfsPafUploadReply(json_decode($result));
    }

    /** @internal
     * @brief Build URL from host, service and other parameters.
     * @param $comment [in] comment associated to this action (default=null).
     * @return full URL to query.
     */
    private function get_url($comment)
    {
        $params = array();
        if (! is_null($comment)) {
            $params['comment'] = $comment;
        }

        return sprintf('%s://%s/bo-ws/service/%d/instance/%s/paf/%s/upload?%s',
            $this->scheme, $this->host, $this->service->id,
            $this->service->status, $this->paf_name,
            $this->format_parameters($params));
    }

    private function set_default_curl_options(&$request, array $headers)
    {
        $default_headers = array('Expect' => '', 'Accept' => 'application/json');
        if (!is_null($headers) && !empty($headers))
            $default_headers = array_merge($default_headers, $headers);

        if (curl_setopt_array($request,
                array(CURLOPT_RETURNTRANSFER => true,
                      //CURLOPT_FAILONERROR => true,
                      CURLOPT_POST => true,
                      CURLOPT_HTTPHEADER => $this->format_http_headers($default_headers),
                      CURLOPT_SSL_VERIFYPEER => false,
                      CURLOPT_SSL_VERIFYHOST => false
                      )) === false) {
            throw new Exception('Cannot define standard query options to send documents');
        }
    }

    private function set_documents_to_send(&$request, AfsDocumentManager $mgr)
    {
        $doc_no = 1;
        $documents = array();
        foreach ($mgr->get_documents() as $doc) {
            $documents['file' . $doc_no] = '@' . $doc->get_filename() . ';type='
                . $doc->get_mime_type() . ';filename=' . basename($doc->get_filename());
            $doc_no++;
        }
        if (curl_setopt($request, CURLOPT_POSTFIELDS, $documents) === false) {
            throw new Exception('Cannot set documents to be sent');
        }
    }

    private function get_bo_version()
    {
        $info_connector = new AfsAboutConnector($this->host, $this->scheme);
        return $info_connector->get_information()->get_gen_version();
        try {
            $info_connector = new AfsAboutConnector($this->host, $this->scheme);
            return $info_connector->get_information()->get_gen_version();
        } catch (Exception $e) {
            throw new AfsBOWSException('Cannot retrieve Back Office information', 1, $e);
        }
    }

    private function format_http_headers(array &$headers)
    {
        $result = array();
        foreach($headers as $key => $value)
            $result[] = $key . ': ' . $value;
        return $result;
    }
}


