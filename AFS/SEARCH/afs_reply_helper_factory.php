<?php
require_once "AFS/SEARCH/afs_text_helper.php";
require_once "AFS/SEARCH/afs_reply_helper.php";
require_once "AFS/SEARCH/afs_promote_reply_helper.php";
require_once "AFS/SEARCH/afs_promote_redirect_reply_helper.php";
require_once "AFS/SEARCH/afs_promote_banner_reply_helper.php";
require_once "COMMON/afs_exception.php";

/** @brief Factory for reply helper. */
class AfsReplyHelperFactory
{
    private $visitor = null;

    /** @brief Constructs new factory instance.
     * @param $visitor [in] visitor used to format title and client data texts.
     */
    public function __construct(AfsTextVisitorInterface $visitor=null)
    {
        $this->visitor = $visitor;
    }

    /** @brief Creates appropriate reply helper.
     *
     * @param $feed [in] name of the feed reply.
     * @param $reply [in] JSON decoded reply used to initialize the helper.
     *
     * @return standard or Promote reply helper.
     */
    public function create($feed, $reply)
    {
        if ('Promote' == $feed) {
            $xmlstring = $reply->clientData[0]->contents;
            $xmlstring = '<promote>' . $xmlstring . '</promote>';
            $clientdata = clone $reply->clientData[0];
            $clientdata->contents = $xmlstring;

            $xmlclientdata = new AfsXmlClientDataHelper($clientdata);


            if ($xmlclientdata instanceof AfsJsonClientDataHelper) {
                $type = $xmlclientdata->get_value('type');
            } elseif ($xmlclientdata instanceof AfsXmlClientDataHelper) {
                $type = $xmlclientdata->get_value('/promote/afs:type', array("afs" => "http://ref.antidot.net/7.3/bo.xsd"));
            }

            switch($type) {
                case "default":
                    return new AfsPromoteReplyHelper($reply);
                case "banner":
                    return new AfsPromoteBannerReplyHelper($reply);
                case "redirection":
                    return new AfsPromoteRedirectReplyHelper($reply);
                    break;
                default:
                    throw new AfsUnknowPromoteTypeException($type);
                    break;
            }
        } else {
            return new AfsReplyHelper($reply, $this->visitor);
        }
    }

    /** @brief Creates list of reply helpers.
     *
     * @param $feed [in] name of the feed reply.
     * @param $replies [in] JSON decoded object which may contain replies.
     *
     * @return list of reply helpers.
     */
    public function create_replies($feed, $replies)
    {
        $result = array();
        if (property_exists($replies, 'reply')) {
            foreach ($replies->reply as $reply)
                $result[] = $this->create($feed, $reply);
        }
        return $result;
    }
}


