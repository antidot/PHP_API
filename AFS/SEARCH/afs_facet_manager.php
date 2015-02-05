<?php
require_once 'AFS/SEARCH/afs_facet.php';
require_once 'AFS/SEARCH/afs_facet_exception.php';
require_once 'AFS/SEARCH/afs_facet_sort.php';
require_once 'AFS/SEARCH/afs_sort_order.php';
require_once 'AFS/SEARCH/afs_facet_values_sort_order.php';
require_once 'COMMON/afs_tools.php';

/** @brief AFS facet manager.
 *
 * Add some control over configured facets:
 * - avoids management of the same facet twice,
 * - allows direct access by facet name.
 */
class AfsFacetManager
{
    private $facets = array();
    private $facet_mode = AfsFacetMode::AND_MODE;
    private $facet_sort_mode = null;
    private $facet_values_sort_order  = null;

    /** @brief Constructs new facet facet manager.
     * @param $other [in] Instance used to initialize new one (default: creates
     *        new instance with default parameters).
     */
    public function __construct(AfsFacetManager $other=null)
    {
        if (! is_null($other)) {
            $this->facets = $other->facets;
            $this->facet_mode = $other->facet_mode;
            $this->facet_sort_mode = $other->facet_sort_mode;
            $this->facet_values_sort_order = $other->facet_values_sort_order;
        }
    }

    /** @name Global facet management
     * @{ */

    /** @brief Defines default facet mode.
     *
     * By default, facet mode is set to AfsFacetMode::OR_MODE.
     *
     * @param $mode [in] Facet mode, see @a AfsFacetMode for more details.
     *
     * @exception InvalidArgumentException when provided mode is invalid.
     */
    public function set_default_facets_mode($mode)
    {
        AfsFacetMode::check_value($mode);
        if (AfsFacetMode::UNSPECIFIED_MODE == $mode)
            throw new InvalidArgumentException('Invalid ' . $mode . ' for default facet mode.');
        $this->facet_mode = $mode;
    }

    /** @brief Retrieves default facet mode.
     * @return facet mode (see AfsFacetMode for more details)
     */
    public function get_default_facets_mode()
    {
        return $this->facet_mode;
    }
    /** @brief Retrieves default stickyness of all facets
     * @return @c true when facets should be sticky, @c false otherwise.
     */
    public function get_default_stickyness()
    {
        return $this->is_mode_sticky($this->facet_mode);
    }

    /** @brief Defines facet sort order.
     * @param $ids [in] List of facet identifiers in the right sort order.
     * @param $mode [in] Sort order mode (see AfsFacetOrder for more details).
     */
    public function set_facet_order(array $ids, $mode)
    {
        AfsFacetOrder::check_value($mode, 'Invalid sort order: ');
        sort_array_by_key($ids, $this->facets, "simple_facet_creator");
        $this->facet_sort_mode = $mode;
    }
    /** @brief Checks whether facet sort order is set to strict mode.
     * @return @c true when facet sort order mode is strict, @c false otherwise.
     */
    public function is_facet_order_strict()
    {
        return AfsFacetOrder::STRICT == $this->facet_sort_mode;
    }

    /** @brief Defines sort order for all facet values.
     *
     * AFS search default sort for facet values is alphanumeric. This method
     * allows to change this behaviour.
     *
     * @param $mode [in] Sort mode (see AfsFacetValuesSortMode).
     * @param $order [in] Sort order (see AfsSortOrder).
     *
     * @exception InvalidArgumentException when $mode or $order is invalid.
     */
    public function set_facets_values_sort_order($mode, $order)
    {
        $this->facet_values_sort_order = new AfsFacetValuesSortOrder($mode, $order);
    }
    /** @brief Checks whether specific sort order has been defined on facet values.
     * @return @c True when specific sort order has been defined, @c false otherwise.
     */
    public function has_facets_values_sort_order()
    {
        return (! is_null($this->facet_values_sort_order));
    }
    /** @brief Retrieves sort order defined on facet values.
     * @return sort order or @c null when no sort sorder has been set.
     */
    public function get_facets_values_sort_order()
    {
        return $this->facet_values_sort_order;
    }
    /** @} */

    /** @name Fine grained facet management
     * @{ */

    /** @brief Defines facet mode for one or more facets.
     * @param $mode [in] Facet mode to set (see AfsFacetMode for more details).
     * @param $ids [in] Identifier(s) of the facet(s).
     * @exception InvalidArgumentException when provided mode is invalid.
     */
    public function set_facets_mode($mode, $ids)
    {
        if (! is_array($ids))
            $ids = array($ids);
        foreach ($ids as $id) {
            if (! array_key_exists($id, $this->facets))
                $this->facets[$id] = new AfsFacet($id, AfsFacetType::UNKNOWN_TYPE);
            $facet = $this->facets[$id];
            $facet->set_mode($mode);
        }
    }
    /** @brief Adds new facet configuration to manager.
     *
     * The order of added facets influences the order of the facets in AFS
     * output reply stream.
     *
     * @param $facet [in] New facet to manage.
     * @exception InvalidArgumentException facet with same id is already
     *            registered.
     */
    public function add_facet(AfsFacet $facet)
    {
        $id = $facet->get_id();
        if (array_key_exists($id, $this->facets)) {
            throw new InvalidArgumentException('Facet with same id (' . $id
                . ') already present.');
        }
        $this->facets[$id] = $facet;
    }
    /** @brief Checks whether provided facet exists and has right parameters.
     *
     * Currently configured facet is updated with parameters of the given facet
     * when it is necessary (update facet mode, facet type...)
     *
     * @param $facet [in] Facet to test.
     * @exception AfsUndefinedFacetException provided facet is not currently
     *            managed.
     * @exception AfsInvalidFacetParameterException provided facet does not
     *            match currently defined parameters.
     */
    public function check_facet(AfsFacet $facet)
    {
        if (! $this->has_facet($facet->get_id())) {
            throw new AfsUndefinedFacetException('No facet with id \''
                . $facet->get_id() . '\' currently managed');
        }
        $configured = $this->get_facet($facet->get_id());
        if (! $configured->update($facet)) {
            throw new AfsInvalidFacetParameterException('Provided facet is not '
                . 'similar to registered one: ' . $facet . ' =/= ' . $configured);
        }
    }
    /** @brief Checks or adds provided facet.
     *
     * If facet of the same id is already managed, this method check that
     * associated parameters are of the good type. Otherwise, the facet is
     * appended to the list of managed facets.
     *
     * @param $facet [in] facet to check/add.
     *
     * @exception AfsInvalidFacetParameterException when provided facet is
     *            incompatible with the one currently registered with same id.
     */
    public function check_or_add_facet($facet)
    {
        try {
            $this->check_facet($facet);
        } catch (AfsUndefinedFacetException $e) {
            $this->add_facet($facet);
        }
    }
    /** @brief Checks whether at least one facet is defined.
     * @return @c true when one or more facet is defined, @c false otherwise.
     */
    public function has_facets()
    {
        return count($this->facets) > 0;
    }
    /** @brief Retrieves all facets.
     * @return all managed facets.
     */
    public function get_facets()
    {
        return $this->facets;
    }
    /** @brief Checks whether facet with provided name has already been defined.
     * @param $name [in] name of the facet to check.
     * @return @c True when facet with provided name is already defined,
     *         @c false otherwise.
     */
    public function has_facet($name)
    {
        if (array_key_exists($name, $this->facets)) {
            return true;
        } else {
            return false;
        }
    }
    /** @brief Retrieves specific facet parameters.
     * @param $name [in] facet name to look for.
     * @return @a AfsFacet instance with required @a name.
     * @exception OutOfBoundsException when no facet with required @a name is
     *            defined.
     */
    public function get_facet($name)
    {
        if (! $this->has_facet($name)) {
            throw new OutOfBoundsException("No facet named '" . $name
                . "' is currently registered");
        }
        return $this->facets[$name];
    }
    /** @brief Retrieves facet, creates it first if it deos not exist.
     *
     * When necessary, facet is created using default configuration parameters.
     *
     * @param $name [in] Facet identifier.
     *
     * @return facet with appropriate identifier.
     */
    public function get_or_create_facet($name)
    {
        try {
            return $this->get_facet($name);
        } catch (OutOfBoundsException $e) {
            $facet = new AfsFacet($name);
            $facet->set_mode($this->get_default_facets_mode());
            $this->add_facet($facet);
            return $this->get_facet($name);
        }

    }
    /**  @} */

    /** @name Internal helpers
     * @{ */

    /** @brief Checks whether provided facet is sticky or not.
     *
     * If facet mode is undefined, rely on default facet mode to determine
     * whether the facet is sticky or not.
     *
     * @param $facet [in] Facet for which mode should be determined.
     *
     * @return @c true when the facet is considered as sticky, @c false
     *         otherwise.
     */
    public function is_sticky(AfsFacet $facet)
    {
        $mode = $facet->get_mode();
        if (AfsFacetMode::UNSPECIFIED_MODE == $mode)
            $mode = $this->facet_mode;

        return $this->is_mode_sticky($mode);
    }

    /** @brief Copies current instance.
     * @return new instance, copy of current one.
     */
    public function copy()
    {
        return new AfsFacetManager($this);
    }
    /** @} */

    private function is_mode_sticky($mode)
    {
        if (AfsFacetMode::SINGLE_MODE == $mode
                || AfsFacetMode::OR_MODE == $mode
                || AfsFacetMode::STICKY_AND_MODE == $mode) {
            return true;
        } else {
            return false;
        }
    }
}


/** @brief Creates AfsFacet object with facet id only.
 *
 * This function is present ofr internal use only!
 *
 * @param $id [in] Identifier of the facet to create.
 * @return newly created facet.
 */
function simple_facet_creator($id)
{
    return new AfsFacet($id, AfsFacetType::UNKNOWN_TYPE);
}
