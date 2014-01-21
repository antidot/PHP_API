<?php
require_once "AFS/SEARCH/afs_coder_interface.php";
require_once "AFS/SEARCH/afs_coder_base.php";

/** @brief Default filter coder implementation. */
class AfsFilterCoder extends AfsCoderBase implements AfsCoderInterface
{
    private $value_sep;
    private $filter_sep;

    /** @brief Construct new instance.
     *
     * @param $value_separator [in] character used to separate values for each
     *        filter.
     * @param $filter_separator [in] character used to separate filters.
     * @param $escape_character [in] character to escape character when filters
     *        or values cointains one of the previous separator.
     *
     * @exception InvalidArgumentException when @a value_separator,
     *            @a filter_separator and @a escape_character are not strictly
     *            different.
     */
    public function __construct($value_separator='_', $filter_separator='-',
        $escape_character='|')
    {
        $this->check_unicity(func_get_args());
        $this->value_sep = $value_separator;
        $this->filter_sep = $filter_separator;
        parent::__construct($escape_character);
    }

    /** @brief Encode filters.
     * @param $filters [in] List of filters with their values.
     * @return string encoded filters
     */
    public function encode(array $filters)
    {
        $result = array();
        foreach ($filters as $filter => $values) {
            $filter_str = array($this->escape($filter));
            foreach ($values as $value) {
                $filter_str[] = $this->escape($value);
            }
            $result[] = implode($this->value_sep, $filter_str);
        }
        return implode($this->filter_sep, $result);
    }

    /** @brief Decode filters from string.
     * @param $filters [in] string representing list of filters with their
     *        values.
     * @return List of filters with their values.
     */
    public function decode($filters)
    {
        $result = array();
        $filters = $this->explode($this->filter_sep, $filters);
        foreach ($filters as $filter) {
            $values = $this->explode($this->value_sep, $filter);
            $filter_name = $this->unescape(array_shift($values));
            $result[$filter_name] = array();
            foreach ($values as $value) {
                $result[$filter_name][] = $this->unescape($value);
            }
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
            . preg_quote($this->filter_sep) . '|' . preg_quote($this->escape)
            . ')', $this->escape . '$1', $value);
    }

    /** @internal
     * @brief Unescape character from filter names and values.
     * @param $value [in] input value to transform.
     * @return unescaped value.
     */
    private function unescape($value)
    {
        return $this->replace(preg_quote($this->escape) . '('
            . preg_quote($this->value_sep) . '|' . preg_quote($this->filter_sep)
            . '|' . preg_quote($this->escape) . ')', '$1', $value);
    }
}
?>
