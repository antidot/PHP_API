<?php
require_once 'AFS/SEARCH/afs_facet_manager.php';
require_once 'COMMON/afs_helper_base.php';
require_once 'AFS/SEARCH/afs_facet_value_formatter.php';
require_once 'COMMON/afs_tools.php';

/** @brief Helper to manage facets. */
class AfsFacetHelper extends AfsHelperBase
{
    private $id = null;
    private $label = null;
    private $layout = null;
    private $type = null;
    private $sticky = null;
    private $elements = null;

    /** @brief Constructs new instance of facet helper.
     *
     * @param $facet [in] root facet element.
     * @param $query [in] @a AfsQuery which has produced current reply.
     * @param $config [in] helper configuration object.
     */
    public function __construct($facet, AfsQuery $query, AfsHelperConfiguration $config)
    {
        $this->id = $facet->id;
        if (property_exists($facet, 'labels') && ! empty($facet->labels)
                && property_exists($facet->labels[0], 'label')) {
            $this->label = $facet->labels[0]->label;
        } else {
            $this->label = $this->id;
        }
        $this->layout = $facet->layout;
        $this->type = $facet->type;
        if (property_exists($facet, 'sticky')
                && 0 == strcmp('true', $facet->sticky)) {
            $this->sticky = true;
        } else {
            $this->sticky = false;
        }
        $facet_manager = $config->get_facet_manager();
        $facet_manager->check_or_add_facet(new AfsFacet($this->id, $this->type,
            $this->layout, AfsFacetMode::REPLACE, AfsFacetCombination::OR_MODE,
            $this->sticky ? AfsFacetStickyness::STICKY : AfsFacetStickyness::NON_STICKY));
        $builder = new AfsFacetElementBuilder($facet_manager, $query);
        $this->elements = $builder->create_elements($this->id, $facet, $config);
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

    /** @brief Retrieves stickyness of the facet.
     * @return return true when the facet is sticky, false otherwise.
     */
    public function is_sticky()
    {
        return $this->sticky;
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
class AfsFacetValueHelper extends AfsHelperBase
{
    /** @brief Label of facet value. */
    public $label = null;
    /** @brief Key of facet value. */
    public $key = null;
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

    /** @brief Meta data associated to facet value. */
    private $meta = null;

    /** @brief Construct new instance. (see class attributes for details) */
    public function __construct($label, $key, $count, $meta, $active, $query,
        $link, $children)
    {
        $this->label = $label;
        $this->key = $key;
        $this->count = $count;
        $this->active = $active;
        $this->query = $query;
        $this->link = $link;
        $this->values = $children;
        $this->meta = $meta;
    }

    /** @brief Retrieves meta data associated to the facet value.
     *
     * @param $name [in] Name of the metadata to retrieve. Default is null which
     *        means retrieves all meta data as array of values (keys correspond
     *        to meta data name, values correspond to meta data value associated
     *        to current facet value).
     *
     * @return required meta data or all meta data (see @a name).
     *
     * @exception OutOfBoundsException when required meta data name does not
     *            exist.
     */
    public function get_meta($name=null)
    {
        if (is_null($name)) {
            return $this->meta;
        } elseif (array_key_exists($name, $this->meta)) {
            return $this->meta[$name];
        } else {
            throw new OutOfBoundsException('No meta data available with name: ' . $name);
        }
    }

    /** @brief Retrieve facet element as array.
     *
     * All data are store in <tt>key => value</tt> format:
     * @li @c label: label of the facet value,
     * @li @c key: key of the facet value,
     * @li @c count: number of element of the facet value,
     * @li @c active: state of the facet value: true when this facet value is 
     * used in current query, false otherwise,
     * @li @c query: query associated to the facet value (see @a query property
     * for more details),
     * @li @c link: link generated from the @a query,
     * @li @c values: list of children facet values. This list is not empty for
     * tree facets only.
     * @li @c meta: key-value pairs of meta data identifiers and values.
     *
     * @remark: When helpers are used to create such facet value, if @a link is
     * generated from @a query, then the query is no more necessary and not 
     * provided. So one of @c query and @c link is null.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        return array_filter(get_object_vars($this), is_not_null);
    }

}


/** @brief Helper to build facet elements.
 *
 * Facet elements are built recursively when necessary. */
class AfsFacetElementBuilder
{
    private $facet_mgr = null;
    private $query = null;

    /** @brief Constructs new instance of facet element builder.
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

    /** @brief Creates recursively facet elements.
     *
     * @param $facet_id [in] current facet id. This value is used to update
     *        current query for each facet element.
     * @param $facet_element [in] starting point used to create facet elements.
     * @param $config [in] helper configuration object.
     *
     * @return list of facet elements (see @ AfsFacetValueHelper).
     */
    public function create_elements($facet_id, $facet_element,
        AfsHelperConfiguration $config)
    {
        $facet = $config->get_facet_manager()->get_facet($facet_id);
        if ((AfsFacetType::STRING_TYPE == $facet->get_type()
                || AfsFacetType::DATE_TYPE == $facet->get_type())
                && AfsFacetLayout::TREE == $facet->get_layout()) {
            $formatter = new AfsQuoteFacetValueIdFormatter();
        } else {
            $formatter = new AfsNoFacetValueIdFormatter();
        }
        return $this->create_elements_recursively($facet_id, $facet_element,
            $formatter, $config);
    }

    /** @internal
     * @brief Creates recursively facet elements.
     *
     * @param $facet_id [in] current facet id. This value is used to update
     *        current query for each facet element.
     * @param $facet_element [in] starting point used to create facet elements.
     * @param $formatter [in] formatter used for facet value identifiers.
     * @param $config [in] helper configuration object.
     *
     * @return list of facet elements (see @ AfsFacetValueHelper).
     */
    private function create_elements_recursively($facet_id, $facet_element,
        AfsFacetValueIdFormatter $formatter, AfsHelperConfiguration $config)
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
                $children = $this->create_elements_recursively($facet_id,
                    $elem, $formatter, $config);
            }

            $value_id = $formatter->format($elem->key);
            $label = $this->extract_label($elem);
            $meta = $this->extract_meta($elem);
            $active = $this->query->has_filter($facet_id, $value_id);
            $query = $this->generate_query($facet_id, $value_id, $active);
            if ($config->has_query_coder()) {
                $link = $config->get_query_coder()->generate_link($query);
                $query = null; // we don't need it anymore
            } else {
                $link = null;
            }
            $helper = new AfsFacetValueHelper($label, $value_id, $elem->items,
                            $meta, $active, $query, $link, $children);
            $elements[] = $config->is_array_format() ? $helper->format() : $helper;

        }
        return $elements;
    }

    private function extract_label($element)
    {
        if (property_exists($element, 'labels')) {
            return $element->labels[0]->label;
        } else {
            return $element->key;
        }
    }

    private function extract_meta($element)
    {
        $result = array();
        if (property_exists($element, 'meta')) {
            foreach($element->meta as $meta) {
                $result[$meta->key] = $meta->value;
            }
        }
        return $result;
    }

    private function generate_query($facet_id, $value_id, $active)
    {
        $result = null;
        $facet = $this->facet_mgr->get_facet($facet_id);
        if ($active) {
            $result = $this->query->remove_filter($facet_id, $value_id);
        } else {
            if ($facet->has_replace_mode()) {
                $result = $this->query->set_filter($facet_id, $value_id);
            } elseif ($facet->has_add_mode()) {
                $result = $this->query->add_filter($facet_id, $value_id);
            } else {
                throw new Exception('Unmanaged facet mode: ' . $facet->get_mode());
            }
        }
        return $result;
    }
}


