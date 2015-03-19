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

    /**
     * @return string, the url used for redirection
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * @brief get the current promote instance type, types are default, banner or redirect
     * @return string 'default', 'banner' or 'redirect'
     */
    public function get_type() {
        return "redirect";
    }
}