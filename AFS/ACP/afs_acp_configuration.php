<?php
require_once 'COMMON/afs_user_session_manager.php';


/** @brief Configuration object for AFS ACP queries.
 */
class AfsAcpConfiguration
{
    private $user_session_mgr = null;


    /** @name User and session management
     * @{ */

    /** @brief Retrieves manager of user and session identifiers.
     * @return configured manager. Default one is instanciated when none has yet
     * been defined.
     */
    public function get_user_session_manager()
    {
        if (is_null($this->user_session_mgr))
            $this->user_session_mgr = new AfsUserSessionManager();
        return $this->user_session_mgr;
    }
    /** @brief Sets new user and session manager.
     * @param $user_session_mgr [in] new manager to set.
     */
    public function set_user_session_manager(AfsUserSessionManager $user_session_mgr)
    {
        $this->user_session_mgr = $user_session_mgr;
    }
    /**  @} */
}
