<?php
require_once 'AIF/afs_bows_information.php';
require_once 'AIF/afs_about_connector.php';


/** Cache Back Office information during PHP script life time. */
class AfsBOWSInformationCache
{
    private static $info = null;


    /** @brief Retrieves Back Office information object.
     *
     * On first call, she queries Back Office otherwise she returns cached
     * object.
     *
     * @param $host [in] Computer name hosting AFS Back Office.
     * @param $scheme [in] Scheme to use to connect to back office.
     *
     * @return AfsBOWSInformation object.
     */
    public static function get_information($host, $scheme)
    {
        if (is_null(self::$info))
            self::query_bo($host, $scheme);
        return self::$info;
    }

    private static function query_bo($host, $scheme)
    {
        try {
            $info_connector = new AfsAboutConnector($host, null, $scheme);
            self::$info = $info_connector->get_information();
        } catch (Exception $e) {
            throw new AfsBOWSException('Cannot retrieve Back Office information', 1, $e);
        }
    }
}
