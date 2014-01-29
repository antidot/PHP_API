<?php

/** @brief Connector interface. */
interface AfsConnectorInterface
{
    /** @brief Send a query.
     *
     * Query is built using provided @a parameters.
     * @param $parameters [in] list of parameters used to build the query.
     * @return reply of the query.
     */
    public function send(array $parameters);
}


