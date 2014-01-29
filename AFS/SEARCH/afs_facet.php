<?php
require_once 'AFS/SEARCH/afs_facet_type.php';
require_once 'AFS/SEARCH/afs_facet_layout.php';
require_once 'AFS/SEARCH/afs_facet_mode.php';
require_once 'AFS/SEARCH/afs_facet_combination.php';
require_once 'AFS/SEARCH/afs_facet_stickyness.php';


/** @brief Configuration class for AFS facets.
 */
class AfsFacet
{
    private $id = null;
    private $type = null;
    private $layout = null;
    private $mode = null;
    private $combination = null;
    private $sticky = null;
    private $embracing_char = '';

    /** @brief Construct new facet with specified parameters.
     *
     * @param $id [in] facet id defined in feed.xml on indexation side.
     * @param $type [in] facet type defined in feed.xml on indexation side (see
     *        @ref AfsFacetType for available types).
     * @param $layout [in] facet layout defined in feed.xml on indexation side.
     *        Default value is AfsFacetLayout::TREE.
     *        (see @ref AfsFacetLayout for availanle layouts).
     * @param $mode [in] facet mode (see @ref AfsFacetMode):
     *        - @c AfsFacetMode::REPLACE (default): when new value of the facet
     *          is selected, it replaces previous one,
     *        - @c AfsFacetMode::ADD : when new value of the facet is selected,
     *          it is combined with previous ones.
     * @param $combination [in] 'and' or 'or' (default) combination used to
     *        combine multiple facet values (see @ref AfsFacetCombination).
     * @param $sticky [in] defines the stickyness of the facet. Default is non
     *        sticky (see AfsFacetStickyness).
     *
     * @exception InvalidArgumentException invalid parameter value provided for
     *            @a type, @a layout, @a mode, @a combination or @a sticky
     *            parameter.
     */
    public function __construct($id, $type, $layout=AfsFacetLayout::TREE,
        $mode=AfsFacetMode::REPLACE, $combination=AfsFacetCombination::OR_MODE,
        $sticky=AfsFacetStickyness::NON_STICKY)
    {
        AfsFacetType::check_value($type, 'Invalid facet type parameter: ');
        AfsFacetLayout::check_value($layout, 'Invalid facet layout parameter: ');
        AfsFacetMode::check_value($mode, 'Invalid facet mode parameter: ');
        AfsFacetCombination::check_value($combination,
            'Invalid facet combination mode parameter: ');
        AfsFacetStickyness::check_value($sticky, 'Invalid facet stickyness parameter: ');

        $this->id = $id;
        $this->type = $type;
        $this->layout = $layout;
        $this->mode = $mode;
        $this->combination = $combination;
        if (AfsFacetLayout::TREE == $this->layout
            && (AfsFacetType::STRING_TYPE == $this->type
                || AfsFacetType::DATE_TYPE == $this->type)) {
            $this->embracing_char = '"';
        }
        $this->sticky = $sticky;
    }

    /** @brief Retrieves facet id.
     * @return facet id.
     */
    public function get_id()
    {
        return $this->id;
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
        return $this->get_mode() == AfsFacetMode::REPLACE;
    }
    /** @brief Check whether mode is set to <tt>add</tt>.
     * @return true when mode is <tt>add</tt>, false otherwise.
     */
    public function has_add_mode()
    {
        return $this->get_mode() == AfsFacetMode::ADD;
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
        return $this->sticky == AfsFacetStickyness::STICKY;
    }

    /** @brief Checks whether provided facet is similar to current instance.
     *
     * Two instances are considered similar when following values are equals:
     * - facet identifier,
     * - facet type,
     * - facet layout.
     * Other facet parameters are not taken into account.
     *
     * @param $other [in] instance to compare with.
     * @return @c True when both instances are similar, @c false otherwise.
     */
    public function is_similar_to(AfsFacet $other)
    {
        if ($this->id == $other->get_id()
                && $this->type == $other->get_type()
                && $this->layout == $other->get_layout()) {
            return true;
        } else {
            return false;
        }
    }
    /** @internal
     * @brief Join all provided @a values and format them appropriately.
     * @param $values [in] array of values to be joined.
     * @return string of joined values.
     */
    public function join_values($values)
    {
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
            . ', ' . $this->mode . ', ' . $this->combination . ', '
            . $this->sticky . '>';
    }
}


