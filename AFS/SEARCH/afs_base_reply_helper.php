<?php
require_once "COMMON/afs_helper_base.php";
require_once "AFS/SEARCH/afs_text_helper.php";

/** @brief Base class for reply helpers. */
class AfsBaseReplyHelper extends AfsHelperBase
{
    protected $reply = null;
    protected $visitor = null;

    /** @brief Constructs new instance.
     * @param $reply [in] should correspond to one reply.
     * @param $visitor [in] visitor used to traverse title and abstract reply
     *        texts.
     */
    public function __construct($reply, AfsTextVisitorInterface $visitor)
    {
        $this->reply = $reply;
        $this->visitor = $visitor;
    }

    /** @brief Retrieves formatted title reply.
     * @return title reply or empty string if not defined.
     */
    public function get_title()
    {
        if (property_exists($this->reply, 'title')) {
            return $this->get_text($this->reply->title);
        } else {
            return '';
        }
    }

    /** @brief Retrieves formatted abstract reply.
     * @return abstract reply or empty string if not defined.
     */
    public function get_abstract()
    {
        if (property_exists($this->reply, 'abstract')) {
            return $this->get_text($this->reply->abstract);
        } else {
            return '';
        }
    }

    /** @brief Retrieves URI of the document.
     * @return document URI or empty string if not set.
     */
    public function get_uri()
    {
        if (property_exists($this->reply, 'uri')) {
            return $this->reply->uri;
        } else {
            return '';
        }
    }

    /** @brief Retrieves reply data as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c title: title of the reply,
     * @li @c abstract: abstract of the reply,
     * @li @c uri: URI of the reply.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        return array('title' => $this->get_title(),
                     'abstract' => $this->get_abstract(),
                     'uri' => $this->get_uri());
    }

    /** @internal
     * @brief Common acces for title and abstract reply formatter.
     * @param $json_text [in] appropriate text block (title/abstract) in JSON
     *        format.
     * @return formatted text.
     */
    protected function get_text($json_text)
    {
        $text_mgr = new AfsTextManager($json_text);
        return $text_mgr->visit_text($this->visitor);
    }
}


