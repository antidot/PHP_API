<?php
require_once "afs_facet.php";

/** @brief AFS facet manager.
 *
 * Add some control over configured facets:
 * - avoids management of the same facet twice,
 * - allows direct access by facet name.
 */
class AfsFacetManager
{
    private $facets = array();

    /** @brief Add new facet configuration to manager.
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
    /** @brief Retrieve all facets.
     * @return all managed facets.
     */
    public function get_facets()
    {
        return $this->facets;
    }
    /** @brief Retrieve specific facet parameters.
     * @param $name [in] facet name to look for.
     * @return @a AfsFacet instance with required @a name.
     * @exception OutOfBoundsException when no facet with required @a name is
     *            defined.
     */
    public function get_facet($name)
    {
        if (! array_key_exists($name, $this->facets)) {
            throw new OutOfBoundsException("No facet named '" . $name
                . "' is currently registered");
        }
        return $this->facets[$name];
    }
}

?>
