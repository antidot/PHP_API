<?php
require_once "afs_helper_base.php";

/** @brief Helper to retrieve useful information from AFS search engine reply header.
 */
class AfsHeaderHelper extends AfsHelperBase
{
    private $header = null;

    /** @brief Constructs AFS search engine reply header helper.
     * @param $header [in] json decoded header reply.
     */
    public function __construct($header)
    {
        $this->header = $header;
    }

    /** @brief Checks whether an error occured.
     *
     * You are encouraged to check error before accessing any other data.
     * @return @c True on error, @c false otherwise.
     */
    public function in_error()
    {
        return property_exists($this->header, 'error');
    }

    /** @brief Retrieves error message.
     *
     * You should check whether an error occurred before retrieving error
     * message otherwise you may go into trouble.
     *
     * @return detailled error.
     */
    public function get_error()
    {
        return $this->header->error->message[0];
    }

    /** @brief Retrieves user identifier.
     *
     * You should check whether an error occurred before retrieving user id
     * otherwise you may go into trouble.
     *
     * @return user identifier.
     */
    public function get_user_id()
    {
        return $this->header->query->userId;
    }

    /** @brief Retrieves session identifier.
     *
     * You should check whether an error occurred before retrieving session id
     * otherwise you may go into trouble.
     *
     * @return session identifier.
     */
    public function get_session_id()
    {
        return $this->header->query->sessionId;
    }

    /** @brief Retrieves AFS search engine computation duration.
     *
     * You should check whether an error occurred before retrieving duration
     * otherwise you may go into trouble.
     *
     * @return computation duration in milliseconds.
     */
    public function get_duration()
    {
        return $this->header->performance->durationMs;
    }
}

?>
