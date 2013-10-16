<?php

/** @brief Language class.
 *
 * This class simplify language management by providing:
 * - simple interface to initilize language code and country code from
 *   litteral string,
 * - simple and direct access to language code,
 * - simple and direct access to country code.
 */
class AfsLanguage
{
    /** @brief Language code in lower case (iso639-1 code) */
    public $lang = null;
    /** @brief Country code in lower case (iso3166-1 code) */
    public $country = null;

    /** @brief Construct AFS language object from string.
     *
     * Language should be composed of two ASCII characters representing
     * language code from iso639-1 standard followed by hyphen (or for
     * convenience by underscore) followed by two characters representing
     * country code from iso3166-1 standard.
     * - Example of valid inputs:
     *   - @c en
     *   - @c en-US
     *   - @c EN-GB
     *   - @c en_gb (for convenience).
     * - Example of invalid inputs:
     *   - @c english
     *   - @c en-USA
     *
     * @remark Even if language code <tt>XX</tt> is invalid, it will not be
     * considered as invalid since it is composed of two letters.
     *
     * @param $lang_str [in] language and country code as string parameter.
     *
     * @exception InvalidArgumentException when provided @a lang_str is invalid.
     */
    public function __construct($lang_str)
    {
        if (is_null($lang_str) || empty($lang_str)) {
            return;
        }

        $matches = null;
        $result = preg_match('/^([a-zA-Z]{2})(?:(?:_|-)((?1)))?$/', $lang_str, $matches);
        if ($result != 1) {
            throw new InvalidArgumentException("Invalid language provided: $lang_str");
        }

        $this->lang = strtolower($matches[1]);
        if (count($matches) > 2) {
            $this->country = strtolower($matches[2]);
        }
    }

    /** @brief String representation of an instance.
     * @return lower case string composed by language code (iso639-1) and
     * country code (iso3166-1) separated by hyphen. Empty string is returned
     * when no language code has been set.
     */
    public function get_string()
    {
        return is_null($this->lang) ? '' : $this->lang
            . (is_null($this->country) ? '' : '-' . $this->country);
    }

    /** @brief String representation of an instance.
     * @return see @a get_string for more details.
     */
    public function __toString()
    {
        return $this->get_string();
    }
}

?>
