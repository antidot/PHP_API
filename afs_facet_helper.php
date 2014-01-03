<?php
require_once "afs_facet_manager.php";
require_once "afs_helper_base.php";

/** @brief Helper to manage facets. */
class AfsFacetHelper extends AfsHelperBase
{
    private $id = null;
    private $label = null;
    private $layout = null;
    private $type = null;
    private $elements = null;

    /** @brief Construct new instance of facet helper.
     *
     * @param $facet [in] root facet element.
     * @param $facet_mgr [in] @a AfsFacetManager with properly configured facets.
     * @param $query [in] @a AfsQuery which has produced current reply.
     * @param $coder [in] @a AfsQueryCoderInterface if set it will be used to
     *        create links (default: null).
     * @param $format [in] if set to AFS_ARRAY_FORMAT (default), format facet
     *        and its values as array, otherwise, helpers are returned as is.
     */
    public function __construct($facet, AfsFacetManager $facet_mgr,
        AfsQuery $query, AfsQueryCoderInterface $coder=null,
        $format=AFS_ARRAY_FORMAT)
    {
        $this->check_format($format);
        $this->id = $facet->id;
        $this->label = $facet->labels[0]->label;
        $this->layout = $facet->layout;
        $this->type = $facet->type;
        $builder = new AfsFacetElementBuilder($facet_mgr, $query);
        $this->elements = $builder->create_elements($this->id, $facet, $coder,
                                $format);
    }

    /** @brief Retrieve facet label.
     *
     * First label found is retrieved. Label in right language is retrieved as
     * soon as filter on required language has been set in @a AfsQuery.
     *
     * @return facet label.
     */
    public function get_label()
    {
        return $this->label;
    }

    /** @brief Retrieves facet id.
     *
     * This value is not necessary unless specific query should be created
     * instead of the ones provided by @a AfsFacetValueHelper.
     *
     * @return the id of the facet.
     */
    public function get_id()
    {
        return $this->id;
    }

    /** @brief Retrieves facet layout.
     * @return layout of the facet (should be AFS_FACET_INTERVAL or
     *         AFS_FACET_TREE).
     */
    public function get_layout()
    {
        return $this->layout;
    }

    /** @brief Retrieves facet type.
     * @return type of the facet (should be one of AFS_FACET_INTEGER,
     *         AFS_FACET_REAL...)
     */
    public function get_type()
    {
        return $this->type;
    }

    /** @brief Retrieve all facet elements of this facet.
     * @return facet elements.
     */
    public function get_elements()
    {
        return $this->elements;
    }

    /** @brief Retrieve facet as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c label: facet label,
     * @li @c values: array of facet elements (see @a AfsFacetValueHelper).
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        return array('label' => $this->get_label(),
                     'values' => $this->get_elements());
    }

}


/** @brief Simple AFS facet value representation. */
class AfsFacetValueHelper
{
    /** @brief Label of facet value. */
    public $label = null;
    /** @brief Number of elements of facet value. */
    public $count = null;
    /** @brief Boolean state of facet value.
     *
     * Set to true when current query filters on this facet value. */
    public $active = null;
    /** @brief Generated query associated to this facet value.
     *
     * When this facet value is active, filter on this facet value is remove
     * from @a query. If this facet value is inactive, filter on this facet
     * value is added to @a query.
     *
     * Added filter replace or complete existing filters depending on facet
     * configuration. */
    public $query = null;
    /** @brief Generated link from above @a query. */
    public $link = null;
    /** @brief List of child facet values.
     *
     * This list is usually empty except for tree facets. */
    public $values = null;

    /** @brief Construct new instance. (see class attributes for details) */
    public function __construct($label, $count, $active, $query, $link,
        $children)
    {
        $this->label = $label;
        $this->count = $count;
        $this->active = $active;
        $this->query = $query;
        $this->link = $link;
        $this->values = $children;
    }

    /** @brief Retrieve facet element as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c label: label of the facet value,
     * @li @c count: number of element of the facet value,
     * @li @c active: state of the facet value: true when this facet value is 
     * used in current query, false otherwise,
     * @li @c query: query associated to the facet value (see @a query property
     * for more details),
     * @li @c link: link generated from the @a query,
     * @li @c values: list of children facet values. This list is not empty for 
     * tree facets only.
     *
     * @remark: When helpers are used to create such facet value, if @a link is
     * generated from @a query, then the query is no more necessary and not 
     * provided. So one of @c query and @c link is null.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        return array_filter(get_object_vars($this),
            function ($value) { return ! is_null($value); });
    }
}


/** @brief Helper to build facet elements.
 *
 * Facet elements are built recursively when necessary. */
class AfsFacetElementBuilder
{
    private $facet_mgr = null;
    private $query = null;

    /** @brief Construct new instance of facet element builder.
     *
     * @param $facet_mgr [in] in conjunction with @a query, it is used to produce
     *        adequate query for each facet element.
     * @param $query [in] query which has led to current reply state.
     */
    public function __construct(AfsFacetManager $facet_mgr, AfsQuery $query)
    {
        $this->facet_mgr = $facet_mgr;
        $this->query = $query;
    }

    /** @brief Create recursively facet elements.
     *
     * @param $facet_id [in] current facet id. This value is used to update
     *        current query for each facet element.
     * @param $facet_element [in] starting point used to create facet elements.
     * @param $coder [in] @a AfsQueryCoderInterface if set it will be used to
     *        create links (default: null).
     * @param $format [in] if set to AFS_ARRAY_FORMAT (default), formats
     *        elements as array, otherwise element objects are kept as is.
     *
     * @return list of facet elements (see @ AfsFacetValueHelper).
     */
    public function create_elements($facet_id, $facet_element,
        AfsQueryCoderInterface $coder=null, $format=AFS_ARRAY_FORMAT)
    {
        $elements = array();

        if (property_exists($facet_element, 'node')) {
            $elem_name = 'node';
        } else {
            $elem_name = 'interval';
        }

        foreach ($facet_element->$elem_name as $elem) {
            // First create children
            $children = array();
            if (property_exists($elem, 'node')) {
                $children = $this->create_elements($facet_id, $elem, $coder, $format);
            }

            if (property_exists($elem, 'labels')) {
                $label = $elem->labels[0]->label;
            } else {
                $label = $elem->key;
            }
            $active = $this->query->has_filter($facet_id, $elem->key);
            $facet = $this->facet_mgr->get_facet($facet_id);
            if ($active) {
                $query = $this->query->remove_filter($facet_id, $elem->key);
            } else {
                if ($facet->has_replace_mode()) {
                    $query = $this->query->set_filter($facet_id, $elem->key);
                } elseif ($facet->has_add_mode()) {
                    $query = $this->query->add_filter($facet_id, $elem->key);
                } else {
                    throw new Exception('Unmanaged facet mode: ' . $facet->get_mode());
                }
            }
            if (is_null($coder)) {
                $link = null;
            } else {
                $link = $coder->generate_link($query);
                $query = null; // we don't need it anymore
            }
            $helper = new AfsFacetValueHelper($label, $elem->items, $active,
                             $query, $link, $children);
            $elements[] = $format == AFS_ARRAY_FORMAT ? $helper->format() : $helper;

        }
        return $elements;
    }
}

?>
