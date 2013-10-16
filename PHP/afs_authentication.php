<?php

/** @defgroup auth_authorities Authentication authorities
 *
 * Authority to use when authenticating on Antidot Back Office Web Service.
 * @{ */
/** @brief LDAP: use LDAP for authentication */
define('AFS_AUTH_LDAP', 'LDPA');
/** @brief BOWS: use internal Back Office authentication manager */
define('AFS_AUTH_BOWS', 'BOWS');
/* @brief SSO: single sign one (should not be used) */
define('AFS_AUTH_SSO', 'SSO');
/* @brief ANTIDOT: internal use only */
define('AFS_AUTH_ANTIDOT', 'Antidot');
/** @} */

/** @brief AFS authentication.
 *
 * Instances of this object should be used to interact with Antidot Back Office
 * APIs. */
class AfsAuthentication
{
    public $user;
    public $password;
    public $authority;

    /** @brief Construct new AFS authentication instance.
     *
     * @param $user [in] login user name.
     * @param $password [in] login password.
     * @param $authority [in] authentication authority to use (see @ref 
     *        auth_authorities).
     */
    public function __construct($user, $password, $authority)
    {
        if (!in_array($authority, array(AFS_AUTH_LDAP, AFS_AUTH_BOWS,
                                        AFS_AUTH_SSO, AFS_AUTH_ANTIDOT))) {
            throw new InvalidArgumentException('Invalid authority parameter');
        }
        $this->user = $user;
        $this->password = $password;
        $this->authority = $authority;
    }
}

?>
