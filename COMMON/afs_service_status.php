<?php
require_once 'COMMON/afs_tools.php';

/** @brief PaF statuses
 *
 * Available status of the PaF and service. */
class AfsServiceStatus extends BasicEnum
{
    private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

    /** @brief Stable: production */
    const STABLE = 'stable';
    /** @brief RC: release candidate, last tests before moving to stable */
    const RC = 'rc';
    /** @brief alpha: first development level */
    const ALPHA = 'alpha';
    /** @brief beta: second development level */
    const BETA = 'beta';
    /** @brief sandbox: test purpose only */
    const SANDBOX = 'sandbox';
    /** @brief archive: no more used in production, kept for reference */
    const ARCHIVE = 'archive';
}


