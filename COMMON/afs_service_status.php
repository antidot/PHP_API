<?php

/** @brief PaF statuses
 *
 * Available status of the PaF and service. */
abstract class AfsServiceStatus
{
    /** @brief Stable: production */
    const STABLE = 'stable';
    /** @brief RC: release candidate, last tests before moving to stable */
    const RC = 'rc';
    /** @brief alpha: first development level */
    const ALPHA = 'alpha';
    /** @brief beta: second development level */
    const BETA = 'beta';
    /** @brief sandbox: test purpose only */
    const SANDBOX = 'sandbox';
    /** @brief archive: no more used in production, kept for reference */
    const ARCHIVE = 'archive';
}


