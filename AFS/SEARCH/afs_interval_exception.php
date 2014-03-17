<?php
require_once 'COMMON/afs_exception.php';

/** @brief Base class for all facet interval exceptions. */
abstract class AfsIntervalException extends AfsBaseException
{ }


/** @brief Exception raised when invalid bound has been provided to interval. */
class AfsIntervalBoundException extends AfsIntervalException
{ }


/** @brief Exception raised when invalid string initializer has been provided to create interval. */
class AfsIntervalInitializerException extends AfsIntervalException
{
    /** @brief Constructs new AfsIntervalInitializerException instance.
     * @param $value [in] Invalid string value.
     */
    public function __construct($value)
    {
        parent::__construct('Cannot initialize interval using: ' . $value);
    }
}
