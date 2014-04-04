<?php

/** @brief Interface used for complex query options.
 *
 * This interface should never be used outside API internals.
 */
interface AfsQueryObjectInterface
{
    /** @brief Produces new instance copied from current one.
     * @return copy of the current instance.
     */
    public function copy();

    /** @brief Format object to appropriate string form.
     *
     * This method can returns single string of array of string when
     * multiple values can be assigned to one single AFS search engine
     * query option.
     *
     * @return formatted object.
     */
    public function format();
}
