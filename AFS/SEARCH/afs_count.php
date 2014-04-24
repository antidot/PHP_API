<?php
require_once 'COMMON/afs_tools.php';

/** @brief Count mode when cluster mode is active.
 *
 * Specify whether reply count should consider documents or clusters.
 */
class AfsCount extends BasicEnum
{
		private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

    /** @brief Count number of documents. */
    const DOCUMENTS = 'documents';
    /** @brief Count number of replies. */
    const CLUSTERS = 'clusters';
}

