<?php
require_once 'AFS/SEARCH/FILTER/afs_filter.php';


/** @brief Factory class to create combinator filter objects.
 */
class AfsCombinatorFactory
{
    /** @brief Creates new combinator filter objects.
     *
     * @param $name [in] Name of the combinator. Allowed names are:
     *        - @c and: to and-combine elements,
     *        - @c or: to or-combine elements.
     * @param $left [in] AfsFilter or AfsGroupFilter object.
     *
     * @return newly created instance of combinator filter object.
     *
     * @exception AfsUnknownCombinatorException when required combinator does not exist.
     */
    public static function create($name, $left)
    {
        if ('and' == $name)
            return new AfsAndCombinatorFilter($left);
        elseif ('or' == $name)
            return new AfsOrCombinatorFilter($left);
        else
            throw new AfsUnknownCombinatorException($name);
    }
}


/** @brief Base class for combinator filter objects.
 */
class AfsBaseCombinatorFilter
{
    private $left = null;
    private $comb_str = null;
    private $right = null;


    /** @brief Constructs new instance of AfsBaseCombinatorFilter.
     *
     * @param $left [in] AfsFilter or AfsGroupFilter object to combine with provided operator.
     * @param $comb_str [in] String representation of the operator (recognized by
     *        AFS search engine).
     */
    public function __construct($left, $comb_str)
    {
        $this->left = $left;
        $this->comb_str = $comb_str;
    }

    /** @brief Creates new filter as the right operand of current combinator.
     * @param $id [in] Filter identifier.
     * @return newly created filter.
     */
    public function filter($id)
    {
        $this->right = new AfsFilter($id, $this);
        return $this->right;
    }

    /** @brief Creates new group as the right operand of current combinator.
     * @param $element [in] Element initialization of the group.
     * @return newly created group.
     */
    public function group($element)
    {
        $this->right = new AfsGroupFilter($element, $this);
        return $this->right;
    }

    /** @brief Retrieves string representation of current instance.
     * @return string representation.
     */
    public function to_string()
    {
        return $this->left->to_string() . ' ' . $this->comb_str . ' ' . $this->right->to_string(true);
    }
}


/** @brief Combinator class for and combination.
 */
class AfsAndCombinatorFilter extends AfsBaseCombinatorFilter
{
    /** @brief Constructs new instance of AfsOrCombinatorFilter.
     * @param $left [in] AfsFilter or AfsGroupFilter used for the combination.
     */
    public function __construct($left)
    {
        parent::__construct($left, 'and');
    }
}

/** @brief Combinator class for or combination.
 */
class AfsOrCombinatorFilter extends AfsBaseCombinatorFilter
{
    /** @brief Constructs new instance of AfsOrCombinatorFilter.
     * @param $left [in] AfsFilter or AfsGroupFilter used for the combination.
     */
    public function __construct($left)
    {
        parent::__construct($left, 'or');
    }
}
