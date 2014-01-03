<?php
/** @defgroup facet_type Facet type
 *
 * Type of the facet use to format facet parameter.
 * @{ */
/** @brief Integer facet type. */
define('AFS_FACET_INTEGER', 'INTEGER');
/** @brief Real facet type. */
define('AFS_FACET_REAL', 'REAL');
/** @brief String facet type. */
define('AFS_FACET_STRING', 'STRING');
/** @brief Date facet type. */
define('AFS_FACET_DATE', 'DATE');
/** @brief Boolean facet type. */
define('AFS_FACET_BOOL', 'BOOL');
/** @brief Interval facets of one of the previous defined type. */
define('AFS_FACET_INTERVAL', 'INTERVAL');
/** @brief Tree facet of one of the previous defined type. */
define('AFS_FACET_TREE', 'TREE');
/**  @} */

/** @defgroup facet_mode Facet mode
 *
 * Mode determines whether each selected facet value replaces previous one or
 * is added to previous filter values.
 * @{ */
/** @brief New value set for the facet should replace any previously defined
 * one. */
define('AFS_FACET_REPLACE', 1);
/** @brief New value set for the facet is added to previously defined one. */
define('AFS_FACET_ADD', 2);
/** @} */

/** @defgroup facet_combination Facet value combination
 *
 * Determines whether facet values are @a OR or @a AND combined.
 * @{ */
/** @brief Values of the facets are OR-combined. */
define('AFS_FACET_OR', 'or');
/** @brief Values of the facets are AND-combined. */
define('AFS_FACET_AND', 'and');
/** @} */

/** @defgroup facet_stickyness Facet stickyness
 *
 * Sticky facets show values and counts for all facet values even if one or more
 * values are already set to filter result set.
 *
 * @remark Stickyness can have already been defined on PaF side.
 * @{ */
/** @brief Facet is sticky. */
define('AFS_FACET_STICKY', 11);
/** @brief Facet is not sticky. */
define('AFS_FACET_NON_STICKY', 12);
/** @} */


/** @brief Configuration class for AFS facets.
 */
class AfsFacet
{
    private $id = null;
    private $type = null;
    private $mode = null;
    private $combination = null;
    private $sticky = null;
    private $embracing_char = '';

    /** @brief Construct new facet with specified parameters.
     *
     * @param $id [in] facet id defined in feed.xml on indexation side.
     * @param $type [in] facet type defined in feed.xml on indexation side (see
     *        @ref facet_type for available types).
     * @param $mode [in] facet mode (see @ref facet_mode):
     *        - @c replace (default): when new value of the facet is selected,
     *          it replaces previous one,
     *        - @c add : when new value of the facet is selected, it is combined
     *          with previous ones.
     * @param $combination [in] 'and' or 'or' (default) combination used to
     *        combine multiple facet values (see @ref facet_combination).
     * @param $sticky [in] defines the stickyness of the facet (see
     *        @ref facet_stickyness). Sticky combination is defaultly set when
     *        this parameter is not provided and facet combination is set to
     *        AFS_FACET_OR.
     *
     * @exception InvalidArgumentException invalid parameter value provided for
     *            @a mode, @a combination or @a sticky parameter.
     */
    public function __construct($id, $type, $mode=AFS_FACET_REPLACE,
        $combination=AFS_FACET_OR, $sticky=null)
    {
        if ($type != AFS_FACET_INTEGER && $type != AFS_FACET_REAL
            && $type != AFS_FACET_STRING && $type != AFS_FACET_DATE
            && $type != AFS_FACET_BOOL && $type != AFS_FACET_INTERVAL) {
            throw new InvalidArgumentException('Facet type parameter should be '
                . 'set to \'AFS_FACET_INTEGER\', \'AFS_FACET_REAL\', '
                . '\'AFS_FACET_STRING\', \'AFS_FACET_DATE\' or '
                . '\'AFS_FACET_BOOL\'');
        } elseif ($mode != AFS_FACET_REPLACE && $mode != AFS_FACET_ADD) {
            throw new InvalidArgumentException('Mode parameter should be set to '
                . '\'AFS_FACET_REPLACE\' or \'AFS_FACET_ADD\'');
        } elseif ($combination != AFS_FACET_OR
                  && $combination != AFS_FACET_AND) {
            throw new InvalidArgumentException('Combination parameter should be'
                . ' set to \'AFS_FACET_OR\' or \'AFS_FACET_AND\'');
        } elseif (! is_null($sticky) && $sticky != AFS_FACET_STICKY
                  && $sticky != AFS_FACET_NON_STICKY) {
            throw new InvalidArgumentException('Sticky parameter should be '
                . 'left empty or set to \'AFS_FACET_STICKY\' or \''
                . 'AFS_FACET_NON_STICKY\'');
        }

        $this->id = $id;
        $this->type = $type;
        $this->mode = $mode;
        $this->combination = $combination;
        if ($this->type == AFS_FACET_STRING
            || $this->type == AFS_FACET_DATE) {
            $this->embracing_char = '"';
        }
        if (is_null($sticky)) {
            if ($combination == AFS_FACET_OR) {
                $sticky = AFS_FACET_STICKY;
            } else {
                $sticky = AFS_FACET_NON_STICKY;
            }
        }
        $this->sticky = $sticky;
    }

    /** @brief Retrieve facet id.
     * @return facet id.
     */
    public function get_id()
    {
        return $this->id;
    }
    /** @brief Retrieve facet mode.
     * @return facet mode (@c replace or @c add).
     */
    public function get_mode()
    {
        return $this->mode;
    }
    /** @brief Check whether mode is set to <tt>replace</tt>.
     * @return true when mode is <tt>replace</tt>, false otherwise.
     */
    public function has_replace_mode()
    {
        return $this->get_mode() == AFS_FACET_REPLACE;
    }
    /** @brief Check whether mode is set to <tt>add</tt>.
     * @return true when mode is <tt>add</tt>, false otherwise.
     */
    public function has_add_mode()
    {
        return $this->get_mode() == AFS_FACET_ADD;
    }
    /** @brief Retrieve facet combination.
     * @return facet combination (@c or or @c and).
     */
    public function get_combination()
    {
        return $this->combination;
    }
    /** @brief Retrieve facet stickyness.
     * @return true when the facet is sticky, false otherwise.
     */
    public function is_sticky()
    {
        return $this->sticky == AFS_FACET_STICKY;
    }
    /** @internal
     * @brief Join all provided @a values and format them appropriately.
     * @param $values [in] array of values to be joined.
     * @return string of joined values.
     */
    public function join_values($values)
    {
        $formatted = array();
        foreach ($values as $value)
        {
            $formatted[] = $this->format_value($value);
        }
        return implode(' ' . $this->combination . ' ', $formatted);
    }
    private function format_value($value)
    {
        return $this->id . '=' . $this->embracing_char . $value
            . $this->embracing_char;
    }
}

?>
