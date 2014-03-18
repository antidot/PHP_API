<?php

/** @brief AFS authentication interface. */
interface AfsAuthentication
{
    /** @brief Formats authentication parameters.
     * @return string representing authentication.
     */
    public function format();
}


