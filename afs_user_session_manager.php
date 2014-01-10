<?php

/** @brief Manages user and session identifiers.
 *
 * These identifiers are stored in cookies which are sent to the client and
 * read from client queries.
 *
 * Default cookie names are used but they can be overloaded while instanciating
 * this class.
 */
class AfsUserSessionManager
{
    private $user_id_cookie = null;
    private $session_id_cookie = null;

    /** @brief Constructs new instance of the manager.
     *
     * @param $user_id_cookie [in] name of the userId cookie (default='AfsUserId').
     * @param $session_id_cookie [in] name of the sessionId cookie
     *        (default='AfsSessionId').
     */
    public function __construct($user_id_cookie='AfsUserId', $session_id_cookie='AfsSessionId')
    {
        $this->user_id_cookie = $user_id_cookie;
        $this->session_id_cookie = $session_id_cookie;
    }

    /** @brief Retrieves user id from cookies.
     * @return cookie corresponding to user identifier or @c null if unset.
     */
    public function get_user_id()
    {
        if (array_key_exists($this->user_id_cookie, $_COOKIE)) {
            return $_COOKIE[$this->user_id_cookie];
        } else {
            return null;
        }
    }

    /** @brief Retrieves session id from cookies.
     * @return cookie corresponding to session identifier or @c null if unset.
     */
    public function get_session_id()
    {
        if (array_key_exists($this->session_id_cookie, $_COOKIE)) {
            return $_COOKIE[$this->session_id_cookie];
        } else {
            return null;
        }
    }

    /** @brief Defines user id cookie.
     *
     * This method should be called before any output is sent to the client.
     *
     * @param $value [in] value to assign to user identifier cookie.
     */
    public function set_user_id($value)
    {
        setcookie($this->user_id_cookie, $value);
    }

    /** @brief Defines session id cookie.
     *
     * This method should be called before any output is sent to the client.
     *
     * @param $value [in] value to assign to session identifier cookie.
     */
    public function set_session_id($value)
    {
        setcookie($this->session_id_cookie, $value);
    }
}

?>
