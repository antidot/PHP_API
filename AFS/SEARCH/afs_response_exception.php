<?php
require_once 'COMMON/afs_exception.php';

/** @brief Base class for all exceptions related to AFS search engine response. */
abstract class AfsResponseException extends AfsBaseException
{ }

/** @brief Exception raised when trying to access object whereas no reply was available. */
class AfsNoReplyException extends AfsResponseException
{ }
