<?php


/** @brief Wrapper to access right underlying object when building filter expression. */
class AfsFilterWrapper
{
    private $parent = null;
    private $obj = null;

    /** @brief Constructs new wrapped object.
     *
     * @param $parent [in] Parent of the wrapper.
     * @param $obj [in] The wrapped object.
     */
    public function __construct($parent, $obj)
    {
        $this->parent = $parent;
        $this->obj = $obj;
    }

    /** @brief Wraps all getter methods and properties calls.
     * @param $name [in] Name of the getter/property.
     * @return This wrapper with updated wrapped object.
     * @exception AfsFilterException when invalid getter/property has been requested
     */
    public function __get($name)
    {
        $this->obj = $this->obj->__get($name);
        return $this;
    }

    /** @brief Wraps all setter methods and properties calls.
     *
     * @param $name [in] Name of the setter/property.
     * @param $params [in] Parameter forwarded to the setter/property/
     *
     * @return This wrapper with updated wrapped object.
     *
     * @exception AfsFilterException when invalid setter/property has been requested
     */
    public function __set($name, $params)
    {
        $this->obj = $this->obj->__set($name, $params);
        return $this;
    }

    /** @brief Wraps all methods calls.
     *
     * Only first method parameter is forwarded to corresponding method of the
     * wrapped object.
     *
     * @param $name [in] Name of the method.
     * @param $params [in] Array of parameters (only the first one is used!).
     *
     * @return This wrapper with updated wrapped object.
     *
     * @exception AfsFilterException when invalid method has been requested.
     */
    public function __call($name, $params)
    {
        // Fortunatelly, the methods accept single parameter!
        $this->obj = $this->obj->$name($params[0]);
        return $this;
    }

    /** @brief Retrieves string representation.
     *
     * @param $serialize_wrapped_object [in] When set to @c true, output 
     *        corresponds to the string representation of the wrapped object.
     *        Otherwise, @c false (default), output corresponds to the string
     *        representation of the parent element.
     *
     * @return string representation of the parent or the wrapped object.
     */
    public function to_string($serialize_wrapped_object=false)
    {
        if ($serialize_wrapped_object)
            return $this->obj->to_string();
        else
            return $this->parent->to_string();
    }
}
