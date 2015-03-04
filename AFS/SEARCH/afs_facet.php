<?php
require_once 'AFS/SEARCH/afs_facet_type.php';
require_once 'AFS/SEARCH/afs_facet_layout.php';
require_once 'AFS/SEARCH/afs_facet_mode.php';
require_once 'AFS/SEARCH/afs_facet_combination.php';


/** @brief Configuration class for AFS facets.
 */
class AfsFacet
{
    private $id = null;
    private $type = null;
    private $layout = null;
    private $mode = null;
    private $combination = null;
    private $values_sort_order = null;

    /** @brief Construct new facet with specified parameters.
     *
     * @param $id [in] facet id defined in feed.xml on indexation side.
     * @param $type [in] facet type defined in feed.xml on indexation side.
     *        Default value is AfsFacetType::UNKNOWN_TYPE.
     *        (see @ref AfsFacetType for available types).
     * @param $layout [in] facet layout defined in feed.xml on indexation side.
     *        Default value is AfsFacetLayout::TREE.
     *        (see @ref AfsFacetLayout for availanle layouts).
     * @param $mode [in] facet mode, see @ref AfsFacetMode for more details.
     *        (default: UNSPECIFIED_MODE).
     *
     * @exception InvalidArgumentException invalid parameter value provided for
     *            @a id, @a type, @a layout or @a mode parameter.
     */
    public function __construct($id, $type=AfsFacetType::UNKNOWN_TYPE,
        $layout=AfsFacetLayout::TREE, $mode=AfsFacetMode::UNSPECIFIED_MODE)
    {
        $this->validate_id($id);
        $this->set_type($type);
        $this->id = $id;
        $this->layout = $layout;
        $this->set_mode($mode);
    }

    /**
     * @brief set values sort order for this facet
     *
     * AFS search default sort for facet values is alphanumeric. This method
     * allows to change this behaviour.
     *
     * @param $mode [in] the mode to use: see AfsFacetValuesSortMode
     * @param $sort [in] the sort to use: see AfsSortOrder
     */
    public function set_values_sort_order($mode, $sort) {
        $this->values_sort_order = new AfsFacetValuesSortOrder($mode, $sort);
    }

    /**
     * @brief get current values sort order for this facet
     * @return AfsFacetValuesSortOrder or null if sort order not specified
     */
    public function get_values_sort_order() {
        return $this->values_sort_order;
    }

    /** @brief Validates facet identifier against official regex.
     * @param $id [in] identifier to validate.
     * @exception InvalidArgumentException when identifier doesn't validate.
     * @exception Exception when bad internal error occures.
     */
    private function validate_id($id)
    {
        $id_pattern = '/^[a-zA-Z][a-zA-Z0-9_:]*$/';
        $result = preg_match($id_pattern, $id);
        if (0 == $result) {
            throw new InvalidArgumentException('Provided facet id(' . $id . ') doesn\'t conform pattern: ' . $id_pattern);
        } elseif (FALSE === $result) {
            throw new Exception('Please contact Antidot support for this PHP API!');
        }
    }

    /** @brief Retrieves facet id.
     * @return facet id.
     */
    public function get_id()
    {
        return $this->id;
    }
    /** @brief Redefines facet type.
     * @param $type [in] new type of the facet to set.
     * @exception InvalidArgumentException invalid type provided.
     */
    public function set_type($type)
    {
        AfsFacetType::check_value($type, 'Invalid facet type parameter: ');
        $this->type = $type;
    }
    /** @brief Retrieves facet type.
     * @return type of the facet.
     */
    public function get_type()
    {
        return $this->type;
    }
    /** @brief Retrieves facet layout.
     * @return layout of the facet.
     */
    public function get_layout()
    {
        return $this->layout;
    }

    /** @brief Defines new facet mode.
     * @param $mode [in] New mode to set.
     * @exception InvalidArgumentException invalid mode provided.
     */
    public function set_mode($mode)
    {
        AfsFacetMode::check_value($mode, 'Invalid facet mode: ');
        $this->mode = $mode;
        if (AfsFacetMode::SINGLE_MODE == $mode
                || AfsFacetMode::OR_MODE == $mode) {
            $this->combination = 'or';
        } elseif (AfsFacetMode::AND_MODE == $mode || AfsFacetMode::STICKY_AND_MODE == $mode) {
            $this->combination = 'and';
        }
    }
    /** @brief Retrieve facet mode.
     * @return facet mode (@c replace, @c or, @c add or @c unspecified).
     */
    public function get_mode()
    {
        return $this->mode;
    }
    /** @brief Check whether mode is set to <tt>single</tt>.
     * @return true when mode is <tt>single</tt>, false otherwise.
     */
    public function has_single_mode()
    {
        return $this->get_mode() == AfsFacetMode::SINGLE_MODE;
    }
    /** @brief Check whether mode is set to <tt>or</tt>.
     * @return true when mode is <tt>or</tt>, false otherwise.
     */
    public function has_or_mode()
    {
        return $this->get_mode() == AfsFacetMode::OR_MODE;
    }
    /** @brief Check whether mode is set to <tt>or</tt>.
     * @return true when mode is <tt>or</tt>, false otherwise.
     */
    public function has_and_mode()
    {
        return $this->get_mode() == AfsFacetMode::AND_MODE;
    }

    public function has_sticky_and_mode()
    {
        return $this->get_mode() == AfsFacetMode::STICKY_AND_MODE;
    }

    /** @brief Checks whether provided facet is similar to current instance.
     *
     * Two instances are considered similar when following values are equals:
     * - facet identifier,
     * - facet type (or one is of unknown type),
     * - facet layout (or one is of unknown layout).
     * Other facet parameters are not taken into account.
     *
     * @param $other [in] instance to compare with.
     * @return @c True when both instances are similar, @c false otherwise.
     */
    public function is_similar_to(AfsFacet $other)
    {
        if ($this->id == $other->get_id()
                && ($this->type == $other->get_type()
                    || $this->type == AfsFacetType::UNKNOWN_TYPE
                    || $other->get_type() == AfsFacetType::UNKNOWN_TYPE)
                && ($this->layout == $other->get_layout()
                    || $this->type == AfsFacetLayout::UNKNOWN
                    || $other->get_layout() == AfsFacetLayout::UNKNOWN)) {
            return true;
        } else {
            return false;
        }
    }

    /** @brief Updates current instance with parameters from other instance.
     *
     * First, both instance are compared then when they are enough similar
     * current instance is updated.<br/>
     * This can be usefull internally when facets are partially declared.
     *
     * @param $other [in] other instance to compare with and use as source to
     *        update.
     *
     * @return @c true when everything goes right, @c false otherwise (instances
     * are not similar).
     */
    public function update(AfsFacet $other)
    {
        if (! $this->is_similar_to($other))
            return false;

        if ($this->type == AfsFacetType::UNKNOWN_TYPE)
            $this->type = $other->get_type();
        if ($this->layout == AfsFacetLayout::UNKNOWN)
            $this->layout = $other->get_layout();
        return true;
    }

    /** @internal
     * @brief Join all provided @a values and format them appropriately.
     * @param $values [in] array of values to be joined.
     * @return string of joined values.
     */
    public function join_values($values)
    {
        // In single mode we should have only one value!
        if ($this->has_single_mode())
            $values = array(array_pop($values));

        $formatted = array();
        foreach ($values as $value) {
            $formatted[] = $this->id . '=' . $value;
        }
        return implode(' ' . $this->combination . ' ', $formatted);
    }

    /** @brief Printable facet.
     *
     * This method should be used for debug purpose only.
     * @return string representation of the facet.
     */
    public function __toString()
    {
        return '<' . $this->id . ': ' . $this->type . ' - ' . $this->layout
            . ', ' . $this->mode . ', ' . $this->combination . '>';
    }
}


