<?php
require_once 'AFS/SEARCH/afs_facet.php';
require_once 'AFS/SEARCH/afs_facet_exception.php';
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
    private $stickyness = false;

    /** @name Global facet management
     * @{ */

    /** @brief Defines stickyness for all facets.
     *
     * By default, facets are not sticky.
     *
     * @param $state [in] stickyness state: @c true (default) to set all facets
     *        sticky, @c false
     */
    public function set_facets_stickyness($state=true)
    {
        $this->stickyness = $state;
    }

    /** @brief Retrieves facet stickyness for all facets
     * @return @c true when facets should be sticky by default, @c false
     * otherwise.
     */
    public function get_facets_stickyness()
    {
        return $this->stickyness;
    }

    /** @brief Defines facet ordering.
     * @param $ids [in] List of facet identifiers in the right order.
     */
    public function set_facet_order(array $ids)
    {
        sort_array_by_key($ids, $this->facets, "simple_facet_creator");
    }
    /** @} */

    /** @name Fine grained facet management
     * @{ */

    /** @brief Defines stickyness for specific facet.
     * @param $id [in] Identifier of the facet.
     * @param $state [in] @c true (default) to set facet sticky, @c false
     *        otherwise.
     */
    public function set_facet_stickyness($id, $state=true)
    {
        if (! array_key_exists($id, $this->facets))
            $this->facets[$id] = new AfsFacet($id, AfsFacetType::UNKNOWN_TYPE);
        $facet = $this->facets[$id];
        $facet->set_sticky($state);
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
     * Currently configured facet is updated with parameters from given facet
     * when it is necessary (update facet stkickyness, facet type...)
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
    /**  @} */
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
