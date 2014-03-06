<?php
require_once 'COMMON/afs_tools.php';

/** @brief Count mode when cluster mode is active.
 *
 * Specify whether reply count should consider documents or clusters.
 */
abstract class AfsCount extends BasicEnum
{
    /** @brief Count number of documents. */
    const DOCUMENTS = 'documents';
    /** @brief Count number of replies. */
    const CLUSTERS = 'clusters';
}

