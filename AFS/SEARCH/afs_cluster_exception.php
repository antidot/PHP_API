<?php
require_once 'AFS/afs_exception.php';

/** @brief Base class for all exceptions related to cluster errors. */
abstract class AfsClusterException extends AfsBaseException
{ }

/** @brief Requested cluster identifier is unknown. */
class AfsUninitializedClusterException extends AfsClusterException
{
    /** @brief Constructs new exception instance. */
    public function __construct()
    {
        parent::__construct('Trying to use or set cluster parameters whereas no cluster has been defined!');
    }
}

