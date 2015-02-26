<?php

/** @brief Base class for AFS exceptions. */
abstract class AfsBaseException extends Exception
{
}


/** @brief Not implemented exception. */
class AfsNotImplementedException extends AfsBaseException
{ }

/**
 * @brief Unknow promote type
 */
class AfsUnknowPromoteTypeException extends  AfsBaseException {
    public function __construct($message) {
        parent::__construct('Unknow promote type: ' . $message);
    }
}
