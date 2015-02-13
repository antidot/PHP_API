<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/13/15
 * Time: 11:05 AM
 */

require_once "AFS/SEARCH/afs_promote_reply_helper.php";


class AfsPromoteRedirectReplyHelper {
    protected $url;

    public function __construct($reply)
    {
        $this->url = $reply->uri;
    }

    public function get_url() {
        return $this->url;
    }
}