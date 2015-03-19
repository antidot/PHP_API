<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 2/13/15
 * Time: 11:04 AM
 */
require_once "AFS/SEARCH/afs_promote_reply_helper.php";


class AfsPromoteBannerReplyHelper extends AfsPromoteReplyHelper {
    protected $url;
    protected $image_url;

    public function __construct($reply)
    {
        parent::__construct($reply);
        $this->image_url = $this->clientdata->get_value('/promote/afs:images/afs:image/afs:imageUrl', array('afs' => 'http://ref.antidot.net/7.3/bo.xsd'));
        $this->url = $this->clientdata->get_value('/promote/afs:images/afs:image/afs:url', array('afs' => 'http://ref.antidot.net/7.3/bo.xsd'));
    }

    /**
     * @return string, the url used for redirection
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * @return string, the url of banner image
     */
    public function get_image_url() {
        return $this->image_url;
    }

    /**
     * @brief get the current promote instance type, types are default, banner or redirect
     * @return string 'default', 'banner' or 'redirect'
     */
    public function get_type() {
        return "banner";
    }
}