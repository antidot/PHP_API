<?php
require_once 'AFS/afs_exception.php';


/** @brief Base class for all ACP exceptions. */
abstract class AfsAcpException extends AfsBaseException
{ }

/** @brief Exception raised when input data can't be used to initialize ACP suggestion. */
class AfsAcpUnmanagedSuggestionFormatException extends AfsAcpException
{ }

/** @brief Exception raised when input data are incoherent. */
class AfsAcpInvalidSuggestionFormatException extends AfsAcpException
{ }

/** @brief Exception raised when trying to initialize replyset from empty ACP suggestion. */
class AfsAcpEmptyReplysetException extends AfsAcpException
{ }
