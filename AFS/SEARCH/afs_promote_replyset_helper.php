<?php
require_once "AFS/SEARCH/afs_base_replyset_helper.php";
require_once "AFS/SEARCH/afs_reply_helper_factory.php";


/** @brief Helper for Promote replies.
 *
 * This helper is very similar to @a AfsReplysetHelper.
 *
 * This helper gives access to underlying helpers for metadata, replies, factes 
 * and pager (if any).
 */
class AfsPromoteReplysetHelper extends AfsBaseReplysetHelper
{
    /** @brief Construct new Promote replyset helper instance.
     *
     * @param $reply_set [in] one reply from decoded json reply.
     * @param $format [in] if set to AfsHelperFormat::ARRAYS (default), all
     *        underlying helpers will be formatted as array of data, otherwise
     *        they are kept as is.
     */
    public function __construct($reply_set, $format=AfsHelperFormat::ARRAYS)
    {
        parent::__construct($reply_set, $format, new AfsReplyHelperFactory());
    }
}

?>
