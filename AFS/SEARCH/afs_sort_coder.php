<?php
require_once "AFS/SEARCH/afs_coder_interface.php";
require_once "AFS/SEARCH/afs_coder_base.php";

/** @brief Default sort coder implementation. */
class AfsSortCoder extends AfsCoderBase implements AfsCoderInterface
{
    private $value_sep;

    /** @brief Construct new instance.
     *
     * @param $value_separator [in] character used to separate values.
     * @param $escape_character [in] character to escape character when sort
     *        parameter contains previous separator.
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

    /** @brief Encodes sort parameters.
     * @param $sorts [in] List of sort parameters.
     * @return string encoded  of sort parameters.
     */
    public function encode(array $sorts)
    {
        $result = array();
        foreach ($sorts as $name => $order) {
            $result[] = $this->escape($name);
            $result[] = $this->escape($order);
        }
        return implode($this->value_sep, $result);
    }

    /** @brief Decodes sort parameters from string.
     * @param $sorts [in] string representing list of sort parameters.
     * @return list of sort parameters.
     * @exception InvalidArgumentException when provided sort value is
     *            malformed.
     */
    public function decode($sorts)
    {
        $result = array();
        $sorts = $this->explode($this->value_sep, $sorts);
        $size = count($sorts);
        if (($size % 2) != 0) {
            throw new InvalidArgumentException('Can\'t decode invalid sort value: ' . $sorts);
        }
        reset($sorts);
        for ($i = 0; $i < $size; $i += 2) {
            $name = $this->unescape(current($sorts));
            $result[$name] = $this->unescape(next($sorts));
            next($sorts);
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

    public function get_separator() {
        return $this->value_sep;
    }
}

