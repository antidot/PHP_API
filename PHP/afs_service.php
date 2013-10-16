<?php

/** @defgroup paf_status PaF statuses
 *
 * Available status of the PaF and service.
 * @{ */
/** @brief Stable: production */
define('AFS_PAF_STABLE', 'stable');
/** @brief RC: release candidate, last tests before moving to stable */
define('AFS_PAF_RC', 'rc');
/** @brief alpha: first development level */
define('AFS_PAF_ALPHA', 'alpha');
/** @brief beta: second development level */
define('AFS_PAF_BETA', 'beta');
/** @brief sandbox: test purpose only */
define('AFS_PAF_SANDBOX', 'sandbox');
/** @brief archive: no more used in production, kept for reference */
define('AFS_PAF_ARCHIVE', 'archive');
/** @} */


/** @brief Antidot service.
 *
 * A service is identified by its service id (numeric value) and its status. */
class AfsService
{
    public $id = null;
    public $status = null;

    /** @brief Construct service object.
     *
     * @param $id [in] identifier of the desired service.
     * @param $status [in] status of the desired service (see @ref paf_status).
     *
     * @exception InvalidArgumentException when @a id or @a status is invalid.
     */
    public function __construct($id, $status=AFS_PAF_STABLE)
    {
        if (! is_numeric($id)) {
            throw new InvalidArgumentException('Service id must be integer');
        } elseif (! in_array($status, array(AFS_PAF_STABLE, AFS_PAF_RC,
                                            AFS_PAF_BETA, AFS_PAF_ALPHA,
                                            AFS_PAF_SANDBOX,AFS_PAF_ARCHIVE))) {
            throw new InvalidArgumentException('Invalid service status provided');
        }
        $this->id = $id;
        $this->status = $status;
    }
}

?>
