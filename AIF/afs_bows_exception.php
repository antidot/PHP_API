<?php
require_once 'COMMON/afs_exception.php';


/** @brief Base class for all Back Office Web Services Exceptions. */
class AfsBOWSException extends AfsBaseException
{ }


/** @brief Exception raised when reply from Back Office seems to be invalid. */
class AfsBOWSInvalidReplyException extends AfsBOWSException
{
    /** @brief Constructs new instance.
     * @param $serialized_reply [in] Serialized reply which can be combined
     *        with error message.
     */
    public function __construct($serialized_reply)
    {
        parent::__construct('Invalid reply: ' . $serialized_reply);
    }
}
