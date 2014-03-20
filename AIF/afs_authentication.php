<?php
/** @file afs_authentication.php */

/** @defgroup auth_authorities Authentication authorities
 *
 * Authority to use when authenticating on Antidot Back Office Web Service.
 * @{ */

/** @brief LDAP: use LDAP for authentication */
define('AFS_AUTH_LDAP', 'LDAP');
/** @brief BOWS: use internal Back Office authentication manager */
define('AFS_AUTH_BOWS', 'BOWS');
/* @brief SSO: single sign one (should not be used) */
define('AFS_AUTH_SSO', 'SSO');
/* @brief ANTIDOT: internal use only */
define('AFS_AUTH_ANTIDOT', 'Antidot');
/** @} */

/** @brief AFS authentication interface. */
interface AfsAuthentication
{
    /** @brief Formats authentication parameters.
     * @param $version [in] Format string representation according to provided
     *        version information.
     * @return string representing authentication.
     */
    public function format_as_url_param($version=null);

    /** @brief Formats authentication parameters.
     * @param $version [in] Format string representation according to provided
     *        version information.
     * @return array representing authentication.
     */
    public function format_as_header_param($version=null);
}


