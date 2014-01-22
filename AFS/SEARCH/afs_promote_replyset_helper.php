<?php
require_once 'AFS/SEARCH/afs_base_replyset_helper.php';
require_once 'AFS/SEARCH/afs_reply_helper_factory.php';
require_once 'AFS/SEARCH/afs_helper_configuration.php';


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
     * @param $config [in] helper configuration object.
     */
    public function __construct($reply_set, AfsHelperConfiguration $config)
    {
        parent::__construct($reply_set, $config, new AfsReplyHelperFactory());
    }
}

?>
