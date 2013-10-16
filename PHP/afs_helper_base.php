<?php

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
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
    }

    protected function check_format($format)
    {
        if ($format != AFS_HELPER_FORMAT && $format != AFS_ARRAY_FORMAT) {
            throw new InvalidArgumentException('Helper format parameter should '
                . 'be set to \'AFS_HELPER_FORMAT\' or \'AFS_ARRAY_FORMAT\'');
        }
    }
}

?>
