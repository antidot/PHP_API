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
        $sticky=null)
    {
        AfsFacetType::check_value($type, 'Invalid facet type parameter: ');
        AfsFacetLayout::check_value($layout, 'Invalid facet layout parameter: ');
        AfsFacetMode::check_value($mode, 'Invalid facet mode parameter: ');
        AfsFacetCombination::check_value($combination,
            'Invalid facet combination mode parameter: ');
        if (! is_null($sticky)) {
            AfsFacetStickyness::check_value($sticky);
        }

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
        if (is_null($sticky)) {
            if (AfsFacetCombination::OR_MODE == $combination) {
                $sticky = AfsFacetStickyness::STICKY;
            } else {
                $sticky = AfsFacetStickyness::NON_STICKY;
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
