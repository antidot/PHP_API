<?php

/** @defgroup uri_scheme Connection scheme
 *
 * Antidot Web Services can be queried in standard HTTP mode or in secured mode
 * (HTTPS).
 * @{ */
/** @brief HTTP: Non secured mode */
define('AFS_SCHEME_HTTP', 'http');
/** @brief HTTPS: Secured mode */
define('AFS_SCHEME_HTTPS', 'https');
/** @} */


/** @brief Base class for AFS connectors.
 *
 * This class provided usefull methods to manage connection strings. */
abstract class AfsConnectorBase
{
    protected function format_parameters(array $parameters)
    {
        $string_parameters = array();
        foreach ($parameters as $name => $values)
        {
            if (is_array($values))
            {
                foreach ($values as $value)
                {
                    $string_parameters[] = urlencode($name) . '='
                        . urlencode($value);
                }
            }
            else
            {
                $string_parameters[] = urlencode($name) . '='
                    . urlencode($values);
            }
        }
        return implode('&', $string_parameters);
    }
}

?>
