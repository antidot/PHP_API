<?php
require_once 'COMMON/afs_exception.php';

/** @brief Base exception class for all client data access errors. */
abstract class AfsClientDataException extends AfsBaseException
{ }

/** @brief Exception raised on malformed expression or invalid context.
 *
 * See DOMXPath::query official documentation for more details. */
class AfsInvalidQueryException extends AfsClientDataException
{ }

/** @brief Exception raised when provided path query has no result. */
class AfsNoResultException extends AfsClientDataException
{ }


