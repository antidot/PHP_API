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
{
    private $query_string = null; ///> search word (or partial word)

    public function __construct($query_string, $message="")
    {
        parent::__construct($message);
        $this->query_string = $query_string;
    }

    /** @brief Retrieves query word for which there is no suggestion.
     * @return query word or partial word.
     */
    public function get_query_string()
    {
        return $this->query_string;
    }
}
