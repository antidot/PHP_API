<?php

require_once 'AFS/afs_query_base.php';


/** @brief Represents an AFS ACP query.
 *
 * All instances of this class are immutable: most of all call involves
 * creation of new instance copied from current one. Newly created instance
 * is modified according to called method and then returned. So, <b>don't
 * forget</b> to store returned object!
 */
class AfsAcpQuery extends AfsQueryBase
{
    /** @internal
     * @brief Copy current instance.
     * @return New copied instance.
     */
    protected function copy()
    {
        return new AfsAcpQuery($this);
    }
}
