<?php
require_once 'COMMON/afs_service_status.php';

/** @brief Antidot service.
 *
 * A service is identified by its service id (numeric value) and its status. */
class AfsService
{
    public $id = null;
    public $status = null;

    /** @brief Constructs service object.
     *
     * @param $id [in] identifier of the desired service.
     * @param $status [in] status of the desired service (see @ref paf_status).
     *
     * @exception InvalidArgumentException when @a id or @a status is invalid.
     */
    public function __construct($id, $status=AfsServiceStatus::STABLE)
    {
        if (! is_numeric($id)) {
            throw new InvalidArgumentException('Service id must be integer');
        }
        AfsServiceStatus::check_value($status, 'Invalid service status provided: ');
        $this->id = $id;
        $this->status = $status;
    }
}

?>
