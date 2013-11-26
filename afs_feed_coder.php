<?php
require_once "afs_coder_interface.php";
require_once "afs_coder_base.php";

/** @brief Default feed coder implementation. */
class AfsFeedCoder extends AfsCoderBase implements AfsCoderInterface
{
    private $value_sep;

    /** @brief Construct new instance.
     *
     * @param $value_separator [in] character used to separate values.
     * @param $escape_character [in] character to escape character when feeds
     *        contains previous separator.
     *
     * @exception InvalidArgumentException when @a value_separator and
     *            @a escape_character are not strictly different.
     */
    public function __construct($value_separator='_', $escape_character='|')
    {
        $this->check_unicity(func_get_args());
        $this->value_sep = $value_separator;
        parent::__construct($escape_character);
    }

    /** @brief Encode feeds
     * @param $feeds [in] List of feeds
     * @return string encoded feeds
     */
    public function encode(array $feeds)
    {
        $result = array();
        foreach ($feeds as $feed) {
            $result[] = $this->escape($feed);
        }
        return implode($this->value_sep, $result);
    }

    /** @brief Decode feeds from string.
     * @param $feeds [in] string representing list of feeds.
     * @return list of feeds.
     */
    public function decode($feeds)
    {
        $result = array();
        $feeds = $this->explode($this->value_sep, $feeds);
        foreach ($feeds as $feed) {
            $result[] = $this->unescape($feed);
        }
        return $result;
    }

    /** @internal
     * @brief Escape special characters.
     * @param $value [in] value to modify.
     * @return @a value with escaped characters.
     */
    private function escape($value)
    {
        return $this->replace('(' . preg_quote($this->value_sep) . '|'
            . preg_quote($this->escape) . ')',
            $this->escape . '$1', $value);
    }

    /** @internal
     * @brief Unescape character from feed names.
     * @param $value [in] input value to transform.
     * @return unescaped value.
     */
    private function unescape($value)
    {
        return $this->replace(preg_quote($this->escape) . '('
            . preg_quote($this->value_sep) . '|' . preg_quote($this->escape)
            . ')', '$1', $value);
    }
}

