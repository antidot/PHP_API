<?php

/** @name afs_version PHP API Version.
 * @{ */
/** @brief API major version number.
 *
 * You should increase this number for new huge features, marketing purpose and
 * when there is a compatibility break with previous version.
 */
define('AFS_API_VERSION_MAJOR', 0);
/** @brief API minor version number.
 *
 * You should increase this number for new small features, code improvements. */
define('AFS_API_VERSION_MINOR', 6);
/** @brief API fix version number.
 *
 * You should increase this number as soon as a bug is fixed.*/
define('AFS_API_VERSION_FIX', 1);

/** @brief API full version number. */
define('AFS_API_VERSION', implode('.', array(AFS_API_VERSION_MAJOR, AFS_API_VERSION_MINOR, AFS_API_VERSION_FIX)));

/** @brief Retrieves PHP API version.
 * @return Human readable string with API version.
 */
function get_api_version()
{
    return 'Antidot PHP API v' . AFS_API_VERSION;
}
/** @} */



