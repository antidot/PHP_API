<?php
require_once "AFS/SEARCH/afs_text_helper.php";
require_once "AFS/SEARCH/afs_reply_helper.php";
require_once "AFS/SEARCH/afs_promote_reply_helper.php";

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
     * @param $visitor [in] visitor used to format title and client data texts.
     *
     * @return standard or Promote reply helper.
     */
    public function create($feed, $reply)
    {
        if ('Promote' == $feed) {
            return new AfsPromoteReplyHelper($reply);
        } else {
            return new AfsReplyHelper($reply, $this->visitor);
        }
    }
}

?>
