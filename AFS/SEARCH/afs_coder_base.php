<?php

/** @brief Base class for coders.
 *
 * Implements usefull methods to simplify encode/decode methods.
 */
abstract class AfsCoderBase
{
    protected $escape;
    protected $regex_delim;

    /** @brief Construct instance with appropriate escape and regex delimiter
     * characters.
     *
     * @param $escape_character [in] character used when values contain
     *        characters with specific meaning (eg: separator)
     * @param $regex_delimiter [in] character used as delimiters in regular
     *        expression.
     */
    public function __construct($escape_character, $regex_delimiter='~')
    {
        $this->escape = $escape_character;
        $this->regex_delim = $regex_delimiter;
    }

    /**
     * @brief Check unicity of provided @a params.
     * @param $params [in] list of parameters to be checked.
     * @exception InvalidArgumentException when @a params are not unique.
     */
    protected function check_unicity(array $params)
    {
        if (count($params) != count(array_unique($params))) {
            throw new InvalidArgumentException('All parameter values must be '
                . 'differents!');
        }
    }

    /**
     * @brief Split provided @a value.
     *
     * Input @a value is splitted on each character separator @a sep except when
     * it is preceded by escape character.
     * @remark escape characters which have been escaped should be unescaped by
     * appropriate function call.
     *
     * @param $sep [in] separator used to split input @a value.
     * @param $value [in] value to be splitted.
     *
     * @return splitted value.
     */
    protected function explode($sep, $value)
    {
        $result = array();
        $escapes = preg_split($this->regex_delim . '[' . $this->escape . ']{2}'
            . $this->regex_delim, $value);
        foreach ($escapes as $escape) {
            $split_on_sep = preg_split($this->regex_delim . '(?<!['
                . $this->escape . "])$sep" . $this->regex_delim,
                $escape);
            $last = array_pop($result);
            if ($last != null) {
                $result[] = $last . $this->escape . $this->escape
                    . array_shift($split_on_sep);
            }
            $result = array_merge($result, $split_on_sep);
        }
        return $result;
    }

    /**
     * @brief Simple wrapper which add surrounding regex delimiters.
     * @param $pattern [in] regex pattern to use (already quoted).
     * @param $replacement [in] replacement.
     * @param $value [in] value to modify.
     * @return updated @a value.
     */
    protected function replace($pattern, $replacement, $value)
    {
        return preg_replace($this->regex_delim
            . str_replace($this->regex_delim, '\\'. $this->regex_delim,
                $pattern)
            . $this->regex_delim, $replacement, $value);
    }
}


