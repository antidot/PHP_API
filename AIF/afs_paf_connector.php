<?php
require_once 'AIF/afs_authentication.php';
require_once 'AIF/afs_document_manager.php';
require_once 'AIF/afs_paf_upload_reply.php';
require_once 'AIF/afs_bows_information_cache.php';
require_once 'COMMON/afs_connector_base.php';

/** @brief AFS PaF connector.
 */
class AfsPafConnector extends AfsBOWSConnector implements AfsBOWSConnectorInterface
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
        if (! $mgr->has_document())
            throw new InvalidArgumentException('No document to be sent');

        $version = $this->get_bo_version();
        $context = new AfsPafConnectorContext($version, $mgr, $comment);
        return new AfsPafUploadReply($this->query($context));
    }

    /** @internal
     * @brief Build URL from host, service and other parameters.
     * @param $context [in] Query context.
     * @return full URL to query.
     */
    public function get_url($context=null)
    {
        $url = parent::get_base_url('service');

        $params = $this->authentication->format_as_url_param($context->version);
        if (! is_null($context->comment))
            $params['comment'] = $context->comment;

        return sprintf($url . '/%d/instance/%s/paf/%s/upload?%s',
            $this->service->id, $this->service->status, $this->paf_name,
            $this->format_parameters($params));
    }

    /** @brief Retrieves authentication as HTTP header for new authentication policy (>=v7.7)
     * @param $context [in] Query context.
     * @return Appropriate HTTP header.
     */
    public function get_http_headers($context=null)
    {
        return $this->authentication->format_as_header_param($context->version);
    }

    public function set_post_content(&$request, $context)
    {
        $mgr = $context->doc_mgr;
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
        return AfsBOWSInformationCache::get_information($this->host,
            $this->scheme)->get_gen_version();
    }

    private function format_http_headers(array &$headers)
    {
        $result = array();
        foreach($headers as $key => $value)
            $result[] = $key . ': ' . $value;
        return $result;
    }
}


/** @brief Context propagated to various function calls.
 */
class AfsPafConnectorContext
{
    public $version = null;
    public $doc_mgr = null;
    public $comment = null;

    /** @brief Constructs new PaF connector context.
     *
     * @param $version [in] Back Office version.
     * @param $doc_mgr [in] Document manager with all documents which should be
     *        sent to Back Office.
     * @param $comment [in] Optional comment for uploaded files.
     */
    public function __construct($version, AfsDocumentManager $doc_mgr, $comment)
    {
        $this->version = $version;
        $this->doc_mgr = $doc_mgr;
        $this->comment = $comment;
    }
}
