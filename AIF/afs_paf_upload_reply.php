<?php
require_once "COMMON/afs_helper_base.php";

/** @brief Wrapper for reply of PaF upload document. */
class AfsPafUploadReply extends AfsHelperBase
{
    private $reply;

    /** @brief Construct PaF upload reply instance.
     *
     * This class should only be instanciated by internal object and never by
     * final user.
     *
     * @param $reply [in] reply of an upload of one or mode documents.
     */
    public function __construct($reply)
    {
        $this->reply = $reply;
    }

    /** @brief Check whether an error occured.
     * @return true on error, false otherwise.
     */
    public function in_error()
    {
        return property_exists($this->reply, 'error');
    }

    /** @brief Retrieve error.
     *
     * When an error occured, you can access to following useful properties:
     * @arg @c code: error code,
     * @arg @c message: error message,
     * @arg @c description: description of the error,
     * @arg @c details: internal details of the error (useful only when
     *      reporting error to Antidot support team).
     *
     * @return error object or null when no error occured.
     */
    public function get_error()
    {
        if ($this->in_error()) {
            return $this->reply->error;
        } else {
            return null;
        }
    }

    /** @brief Check whether command execution succeded.
     * @return true when there is no error, false otherwise.
     */
    public function has_result()
    {
        return property_exists($this->reply, 'result');
    }

    /** @brief Retrieve result of the command.
     *
     * When a result is available, you can access following useful properties:
     * @arg @c jobId: job identifier which can be used later to check whether
     *      the job succeeded,
     * @arg @c started: state of the job (boolean).
     *
     * @return result object or null when an error occured.
     */
    public function get_result()
    {
        if ($this->has_result()) {
            return $this->reply->result;
        } else {
            return null;
        }
    }
}


