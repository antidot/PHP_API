<?php
require_once 'AFS/afs_origin.php';
require_once 'COMMON/afs_helper_format.php';

/** @brief Helper representing one suggestion reply.
 *
 * This class is not intended to be instanciated manually.
 */
class AfsAcpReplyHelper extends AfsHelperBase
{
    private $value = null;
    private $meta = null;


    /** @brief Constructs new ACP suggestion helper.
     *
     * @param $suggestion [in] Suggestion value.
     * @param $meta [in] Meta data associated to the suggestion value (may be null).
     * @param $config [in] ACP configuration.
     */
    public function __construct($suggestion, $meta, AfsAcpConfiguration $config=null)
    {
        $this->value = $suggestion;
        $this->meta = (is_null($meta) ? array() : $meta);
    }

    /** @brief Retrieves suggestion value.
     * @return value of the suggestion.
     */
    public function get_value()
    {
        return $this->value;
    }

    /** @brief Gets AFS search query associated to this suggestion value.
     *
     * AFS search query is initialized with appropriate search word and @c from
     * parameter is set to AfsOrigin::ACP.
     *
     * @return AFS search query.
     */
    public function get_search_query()
    {
        $query = new AfsQuery();
        $query = $query->set_query($this->value)->set_from(AfsOrigin::ACP);
        return $query;
    }

    /** @name Meta data
     * @{ */

    /** @brief Checks whether mete data (options) are available.
     *
     * @param $name [in] name of meta data to check. Default is null to test
     *        whether at least one meta data is available.
     * @return @c true when required meta data is present or least one meta data
     *         is available, @c false otherwise.
     */
    public function has_option($name=null)
    {
        if (is_null($name))
            return ! empty($this->meta);
        elseif (array_key_exists($name, $this->meta))
            return true;
        else
            return false;
    }
    /** @brief Retrieves specified meta data.
     * @param $name [in] name of the requested meta data.
     * @return meta data.
     * @exception OutOfBoundsException when requested meta data is unavailable.
     */
    public function get_option($name)
    {
        if (array_key_exists($name, $this->meta))
            return $this->meta[$name];
        else
            throw new OutOfBoundsException('No meta data available for suggestion: ' .$this->value);
    }
    /** @brief Retrieves all meta data associated to current suggestion value.
     * @return meta data as key-value pairs.
     */
    public function get_options()
    {
        return $this->meta;
    }
    /** @} */

    /** @name Miscellaneous
     * @{ */

    /** @brief Retrieves suggestions as array.
     *
     * This method is intended for internal use only.
     *
     * All data are stored in <tt>key => value</tt> format:
     * @li @c value: suggestion value,
     * @li @c options: map of meta data key-value pairs.
     *
     * @return array filled with key and values.
     */
    public function format()
    {
        return array('value' => $this->value,
                     'options' => $this->meta);
    }
}
