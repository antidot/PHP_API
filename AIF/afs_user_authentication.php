<?php
require_once 'AIF/afs_authentication.php';

/** @brief AFS user authentication.
 *
 * Instances of this class should be used t interact with Antidot Back Office
 * APIs.
 */
class AfsUserAuthentication implements AfsAuthentication
{
    private $user = null;
    private $password = null;


    /** @brief Constructs new AFS user authentication instance.
     *
     * @param $user [in] Login user name.
     * @param $password [in] Login password.
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /** @brief Formats authentication parameters.
     * @return string representing authentication.
     */
    public function format()
    {
        return base64_encode($this->user . ':' . $this->password);
    }
}
