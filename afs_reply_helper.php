<?php
require_once "afs_text_helper.php";
require_once "afs_text_visitor.php";
require_once "afs_helper_base.php";
require_once "afs_client_data_helper.php";

/** @brief Helper to manager title, abstract and uri of one reply.
 *
 * You are @b highly encouraged to use this helper to format each reply.
 *
 * This helper use same visitor for both title and abstract reply. If none is
 * defined while constructing instance, default implementation is used (see
 * @a AfsTextVisitor).
 *
 * In order to deal with client data, you should have to use specific @a
 * AfsClientDataManager.
 */
class AfsReplyHelper extends AfsHelperBase
{
    private $reply = null;
    private $visitor = null;

    /** @brief Construct new instance.
     *
     * @param $reply [in] should correspond to one reply.
     * @param $visitor [in] (optional) visitor used to traverse title and
     *        abstract reply texts. Default implementation is used
     *        (@a AfsTextVisitor) when none is provided.
     */
    public function __construct($reply, AfsTextVisitorInterface $visitor=null)
    {
        if ($visitor == null) {
            $visitor = new AfsTextVisitor();
        }

        $this->reply = $reply;
        $this->visitor = $visitor;
    }

    /** @brief Retrieve formatted title reply.
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

    /** @brief Retrieve formatted abstract reply.
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

    /** @brief Retrieve URI of the document.
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

    /** @brief Retrieves client data manager.
     *
     * @return Manager of client data (see AfsClientDataManager).
     *
     * @exception Exception when no client data is available.
     */
    public function get_clientdatas()
    {
      if (array_key_exists('clientData', $this->reply)) {
          return new AfsClientDataManager($this->reply->clientData);
      } else {
          throw new Exception('No client data available!');
      }
    }

    /** @brief Retrieves specific client data.
     *
     * @param $id [in] Id of the client data to retrieve (default: 'main').
     * @return Client data helper with appropriate id
     *
     * @exception OutOfBoundsException when required client data is not found.
     */
    public function get_clientdata($id='main')
    {
        return $this->get_clientdatas()->get_clientdata($id);
    }

    /** @brief Retrieve reply data as array.
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
    private function get_text($json_text)
    {
        $text_mgr = new AfsTextManager($json_text);
        return $text_mgr->visit_text($this->visitor);
    }
}

?>
