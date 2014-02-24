<?php
require_once 'COMMON/afs_helper_base.php';

/** @brief Base class for response helpers.
 *
 * Manages error. */
abstract class AfsResponseHelperBase extends afsHelperBase
{
    protected  $error = null;


    /** @name Error
     * @{ */

    /** @brief Check whether an error has been raised.
     * @return True on error, false otherwise.
     */
    public function in_error()
    {
        return ! is_null($this->error);
    }

    /** @brief Retrieve error message.
     * @return Error message.
     */
    public function get_error_msg()
    {
        return $this->error;
    }

    /** @internal
     * @brief Defines error message.
     * @param $msg [in] new error message to set.
     */
    protected function set_error_msg($msg)
    {
        $this->error = $msg;
    }
    /** @} */
}
