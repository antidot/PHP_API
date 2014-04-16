<?php
require_once "COMMON/afs_helper_format.php";

/** @brief Add @e magic so that getter methods can be called the same way as
 * properties. */
abstract class AfsHelperBase
{
    /** @brief Simple property helper.
     *
     * Convenient way to access <tt>get_XXX</tt> methods through property call.
     *
     * @param $name [in] name of the required property.
     * @return value of the property.
     */
    public function __get($name)
    {
        $getter = 'get_' . $name;
        if (is_callable(array($this, $getter))) {
            return $this->$getter();
        } else {
            throw new Exception("Undefined property: $name");
        }
    }
}


