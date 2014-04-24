<?php
require_once "COMMON/afs_tools.php";

/** @brief Helper format
 *
 * Specify in which format helpers are generated.
 */
class AfsHelperFormat extends BasicEnum
{
 		private static $instance = null;

    static public function check_value($value, $msg=null)
    {
        if (is_null(self::$instance))
            self::$instance = new self();
        BasicEnum::check_val(self::$instance, $value, $msg);
    }

   /** @brief Outputs from response helper and sub-sequent child helpers are 
     * instances of helper classes. */
    const HELPERS = 0;
    /** @brief Outputs from response helper and sub-sequent child helpers are 
     * array of key/value pairs.
     *
     * This is the prefered format to use in combination with PHP template engines. 
     */
    const ARRAYS = 1;
}


