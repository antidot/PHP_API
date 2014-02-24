<?php

/** @brief Base class for AFS exceptions. */
abstract class AfsBaseException extends Exception
{ }

/** @brief Exception class for internal use only. */
class AfsConnectorExecutionFailedException extends AfsBaseException
{ }
