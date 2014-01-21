<?php
require_once "AFS/SEARCH/afs_base_reply_helper.php";
require_once "AFS/SEARCH/afs_text_helper.php";
require_once "AFS/SEARCH/afs_text_visitor.php";
require_once "AFS/SEARCH/afs_client_data_helper.php";
require_once "COMMON/afs_helper_base.php";

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
class AfsReplyHelper extends AfsBaseReplyHelper
{
    /** @brief Construct new instance.
     *
     * @param $reply [in] should correspond to one reply.
     * @param $visitor [in] (optional) visitor used to traverse title and
     *        abstract reply texts. Default implementation is used
     *        (@a AfsTextVisitor) when none is provided.
     */
    public function __construct($reply, AfsTextVisitorInterface $visitor=null)
    {
        parent::__construct($reply, is_null($visitor) ? new AfsTextVisitor() : $visitor);
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
}

?>
