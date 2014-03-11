<?php
require_once 'AFS/SEARCH/FILTER/afs_valued_filter.php';
require_once 'AFS/SEARCH/FILTER/afs_filter_exception.php';


/** @brief Factory class to create operator filter objects.
 */
class AfsOperatorFactory
{
    /** @brief Creates new operator filter objects
     *
     * @param $name [in] Name of the operator. Allowed names are:
     *        - @c equal: equal comparison,
     *        - @c not_equal: not equal comparison,
     *        - @c less: less than comparison,
     *        - @c less_equal: less than or equal comparison,
     *        - @c greater: greater than comparison,
     *        - @c greater_equal: greater than or equal comparison.
     * @param $filter [in] AfsFilter object.
     *
     * @return newly created instance of operator filter object.
     *
     * @exception AfsUnknownOperatorException when required operator does not exist.
     */
    public static function create($name, AfsFilter $filter)
    {
        if ('equal' == $name)
            return new AfsEqualOperatorFilter($filter);
        elseif ('not_equal' == $name)
            return new AfsNotEqualOperatorFilter($filter);
        elseif ('less' == $name)
            return new AfsLessOperatorFilter($filter);
        elseif ('less_equal' == $name)
            return new AfsLessEqualOperatorFilter($filter);
        elseif ('greater' == $name)
            return new AfsGreaterOperatorFilter($filter);
        elseif ('greater_equal' == $name)
            return new AfsGreaterEqualOperatorFilter($filter);
        else
            throw new AfsUnknownOperatorException($name);
    }
}


/** @brief Base class for operator filter objects.
 */
abstract class AfsBaseOperatorFilter
{
    private $filter = null;
    private $op_str = null;


    /** @brief Constructs new instance of AfsBaseOperatorFilter
     *
     * @param $filter [in] AfsFilter object to combine with provided operator.
     * @param $op_str [in] String representation of the operator (recognized by
     *        AFS search engine).
     */
    public function __construct(AfsFilter $filter, $op_str)
    {
        $this->filter = $filter;
        $this->op_str = $op_str;
    }

    /** @brief Associates a value to current instance and create an AfsValuedFilter.
     * @param $value [in] Value to assign to AfsValuedFilter.
     * @return newly created AfsValuedFilter instance.
     */
    public function value($value)
    {
        return new AfsValuedFilter($this, $value);
    }

    /** @brief Transforms this instance in its string representation.
     * @return string representation of the instance.
     */
    public function to_string()
    {
        return $this->filter->to_string() . $this->op_str;
    }
}


/** @brief Operator class for equality comparison.
 */
class AfsEqualOperatorFilter extends AfsBaseOperatorFilter
{
    /** @brief Constructs new instance of AfsEqualOperatorFilter.
     * @param $filter [in] AfsFilter used for equal comparison.
     */
    public function __construct(AfsFilter $filter)
    {
        parent::__construct($filter, '=');
    }
}

/** @brief Operator class for inequality comparison.
 */
class AfsNotEqualOperatorFilter extends AfsBaseOperatorFilter
{
    /** @brief Constructs new instance of AfsNotEqualOperatorFilter.
     * @param $filter [in] AfsFilter used for not equal comparison.
     */
    public function __construct(AfsFilter $filter)
    {
        parent::__construct($filter, '!=');
    }
}

/** @brief Operator class for less than comparison.
 */
class AfsLessOperatorFilter extends AfsBaseOperatorFilter
{
    /** @brief Constructs new instance of AfsLessOperatorFilter.
     * @param $filter [in] AfsFilter used for less than comparison.
     */
    public function __construct(AfsFilter $filter)
    {
        parent::__construct($filter, '<');
    }
}

/** @brief Operator class for less than or equal to comparison.
 */
class AfsLessEqualOperatorFilter extends AfsBaseOperatorFilter
{
    /** @brief Constructs new instance of AfsLessEqualOperatorFilter.
     * @param $filter [in] AfsFilter used for less than or equal to comparison.
     */
    public function __construct(AfsFilter $filter)
    {
        parent::__construct($filter, '<=');
    }
}

/** @brief Operator class for greater than comparison.
 */
class AfsGreaterOperatorFilter extends AfsBaseOperatorFilter
{
    /** @brief Constructs new instance of AfsGreaterOperatorFilter.
     * @param $filter [in] AfsFilter used for greater than comparison.
     */
    public function __construct(AfsFilter $filter)
    {
        parent::__construct($filter, '>');
    }
}

/** @brief Operator class for greater than or equal to comparison.
 */
class AfsGreaterEqualOperatorFilter extends AfsBaseOperatorFilter
{
    /** @brief Constructs new instance of AfsGreaterEqualOperatorFilter.
     * @param $filter [in] AfsFilter used for greater than or equal to comparison.
     */
    public function __construct(AfsFilter $filter)
    {
        parent::__construct($filter, '>=');
    }
}
