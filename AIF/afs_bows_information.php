<?php
require_once 'AIF/afs_bows_exception.php';


/** @brief Back Office information class.
 *
 * Allows to access to Back Office version numbers.
 */
class AfsBOWSInformation
{
    private $version_info = null;

    /** @brief Constructs new AFS Back Office Web Services information instance.
     * @param $json_decoded [in] Json decoded reply of appropriate Web Service.
     */
    public function __construct($json_decoded)
    {
        if (!property_exists($json_decoded, 'result')
                || !property_exists($json_decoded->result, 'boWsVersion'))
            throw new AfsBOWSInvalidReplyException(serialize($json_decoded));
        $this->version_info = $json_decoded->result->boWsVersion;
    }

    /** @brief Retrieves GEN version.
     * @return GEN version.
     */
    public function get_gen_version()
    {
        return $this->version_info->gen;
    }

    /** @brief Retrieves major version number.
     * @return Major version number.
     */
    public function get_major_version()
    {
        return $this->version_info->major;
    }

    /** @brief Retrieves minor version number.
     * @return Major version number.
     */
    public function get_minor_version()
    {
        return $this->version_info->minor;
    }
}
