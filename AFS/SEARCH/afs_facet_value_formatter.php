<?php

/** @brief Interface used to format facet values.  */
interface AfsFacetValueIdFormatter
{
    /** @brief Formats input value
     * @param $value [in] value to be formatted.
     * @return formatted value.
     */
    public function format($value);
}


/** @brief Simple formatter which does nothing. */
class AfsNoFacetValueIdFormatter implements AfsFacetValueIdFormatter
{
    /** @brief Does nothing: return input value.
     * @param $value [in] value to be formatted.
     * @return input value.
     */
    public function format($value)
    {
        return $value;
    }
}


/** @brief Formatter which surround value with double quotes. */
class AfsQuoteFacetValueIdFormatter implements AfsFacetValueIdFormatter
{
    /** @brief Format input value.
     * @param $value [in] vaalue to be formatted.
     * @return input value surrounded by double quotes.
     */
    public function format($value)
    {
        return '"' . $value . '"';
    }
}

?>
