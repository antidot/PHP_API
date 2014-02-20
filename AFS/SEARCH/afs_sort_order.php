<?php
require_once 'COMMON/afs_tools.php';

/** @brief Sort order pseudo-enumerator. */
class AfsSortOrder extends BasicEnum
{
    /** @brief Sort in descending order. */
    const DESC = 'DESC';
    /** @brief Sort in ascending order. */
    const ASC = 'ASC';
}
