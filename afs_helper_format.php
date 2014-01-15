<?php
require_once "afs_tools.php";

/** @brief Helper format
 *
 * Specify in which format helpers are generated.
 */
abstract class AfsHelperFormat extends BasicEnum
{
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

?>
