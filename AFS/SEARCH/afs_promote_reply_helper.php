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
    protected $clientdata = null;

    /** @brief Constructs new instance.
     * @param $reply [in] one reply used to initialize the instance.
     */
    public function __construct($reply)
    {
        parent::__construct($reply, new AfsRawTextVisitor());
        $xmlstring = $reply->clientData[0]->contents;
        $xmlstring = '<promote>' . $xmlstring . '</promote>';
        $clientdata = clone $reply->clientData[0];
        $clientdata->contents = $xmlstring;

        $clientdata = new AfsXmlClientDataHelper($clientdata);

        $this->clientdata = $clientdata;
    }

    /**
     * @brief get the current promote instance type, types are default, banner or redirect
     * @return string 'default', 'banner' or 'redirect'
     */
    public function get_type() {
        return 'default';
    }

    /** @brief Retrieves custom data from promote reply.
     *         To call this method, get_type should return 'default'
     * @param $key [in] Identifier of the custom resource. When not specified
     *        custom data are returned as key/value pairs.
     * @return value(s) associated to specified key or all key/value pairs.
     * @exception Exception no custom data has been defined.
     */
    public function get_custom_data($key=null)
    {
        if (is_null($this->clientdata)) {
            throw new Exception('No custom data available for this promote ('
                                . $this->get_title() . ')');
        }

        if (is_null($key)) {
            return $this->extract_key_value_pairs();
        } else {
            return $this->clientdata->get_value("/promote/afs:customData/afs:$key",
                array('afs' => 'http://ref.antidot.net/7.3/bo.xsd'));
        }
    }

    private function extract_key_value_pairs()
    {
        $result = array();
        $customdata = $this->clientdata->get_node('/promote/afs:customData', array("afs" => "http://ref.antidot.net/7.3/bo.xsd"));

        foreach (array_shift($customdata) as $key => $value) {
            $result[$key] = $value;
        }


        return $result;
    }
}


