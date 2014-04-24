<?php
require_once 'COMMON/afs_tools.php';

/** @brief Sort order pseudo-enumerator. */
class AfsSortOrder extends BasicEnum
{
 		private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

   /** @brief Sort in descending order. */
    const DESC = 'DESC';
    /** @brief Sort in ascending order. */
    const ASC = 'ASC';
}
