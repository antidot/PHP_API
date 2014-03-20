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
    private $authority = null;


    /** @brief Constructs new AFS user authentication instance.
     *
     * @param $user [in] Login user name.
     * @param $password [in] Login password.
     * @param $authority [in] Authentication authority to use (see @ref
     *        auth_authorities). This parameter is useful and mandatory for
     *        AFS search engine 7.6.
     */
    public function __construct($user, $password, $authority=null)
    {
        $this->user = $user;
        $this->password = $password;
        $this->authority = $authority;
    }

    /** @brief Formats authentication parameters.
     * @param $version [in] Format string representation according to provided
     *        version information.
     * @return array representing authentication.
     */
    public function format_as_url_param($version=null)
    {
        if ('7.6' == $version) {
            if (is_null($this->authority))
                throw new InvalidArgumentException('In version ' . $version
                . ' authority parameter is mandatory!');
            return array('afs:login' => sprintf('login://%s:%s@%s', $this->user, $this->password, $this->authority));
        } else {
            return array();
        }
    }
    /** @brief Formats authentication parameters.
     * @param $version [in] Format string representation according to provided
     *        version information.
     * @return array representing authentication.
     */
    public function format_as_header_param($version=null)
    {
        if ('7.6' == $version)
            return array();
        else
            return array('Authorization' => 'Basic ' . base64_encode($this->user . ':' . $this->password));
    }
}
