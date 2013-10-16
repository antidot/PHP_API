<?php

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
