<?php
require_once "AFS/SEARCH/afs_raw_text_visitor.php";
require_once "AFS/SEARCH/afs_base_reply_helper.php";
require_once "AFS/SEARCH/afs_client_data_helper.php";

/** @brief Promote helper to manager title, abstract and uri of one reply.
 *
 * You are @b highly encouraged to use this helper to format Promote replies.
 *
 * This helper use same visitor for both title and abstract reply. If none is
 * defined while constructing instance, default implementation is used (see
 * @a AfsTextVisitor).
 *
 * In order to deal with client data, you should have to use specific @a
 * AfsClientDataManager.
 */
class AfsPromoteReplyHelper extends AfsBaseReplyHelper
{
    private $clientdata_mgr = null;

    /** @brief Constructs new instance.
     * @param $reply [in] one reply used to initialize the instance.
     */
    public function __construct($reply)
    {
        parent::__construct($reply, new AfsRawTextVisitor());
        if (property_exists($this->reply, 'clientData')) {
            $this->clientdata_mgr = new AfsClientDataManager($this->reply->clientData);
        }
    }

    /** @brief Retrieves custom data from promote reply.
     * @param $key [in] Identifier of the custom resource. When not specified
     *        custom data are returned as key/value pairs.
     * @return value(s) associated to specified key or all key/value pairs.
     * @exception Exception no custom data has been defined.
     */
    public function get_custom_data($key=null)
    {
        if (is_null($this->clientdata_mgr)) {
            throw new Exception('No custom data available for this promote ('
                                . $this->get_title() . ')');
        }

        $clientdata = null;
        try {
            $clientdata = $this->clientdata_mgr->get_clientdata();
        } catch (OutOfBoundsException $e) {
            throw new Exception('Custom data with default identifier is not available!');
        }

        if ('application/xml' != $clientdata->get_mime_type()) {
            throw new Exception('Custom data is not store in XML format, update'
                . ' your PHP connector or contact Antidot support team.');
        }

        if (is_null($key)) {
            return $this->extract_key_value_pairs($clientdata);
        } else {
            return $clientdata->get_value("/afs:customData/afs:$key",
                array('afs' => 'http://ref.antidot.net/7.3/bo.xsd'));
        }
    }

    private function extract_key_value_pairs($clientData)
    {
        $result = array();
        $doc = new DOMDocument();
        $doc->loadXML($clientData->get_value());
        if ($doc->hasChildNodes() && $doc->childNodes->item(0)->hasChildNodes()) {
            foreach ($doc->childNodes->item(0)->childNodes as $node) {
                $result[$node->localName] = $node->nodeValue;
            }
        }
        return $result;
    }
}


