<?php
require_once "afs_replyset_helper.php";
require_once "afs_helper_base.php";

/** @defgroup helper_format Helper format
 *
 * Specify in which format helpers are generated.
 * @{ */
/** @brief Outputs from response helper and sub-sequent child helpers are 
 * instances of helper classes. */
define('AFS_HELPER_FORMAT', 1);
/** @brief Outputs from response helper and sub-sequent child helpers are 
 * array of key/value pairs.
 *
 * This is the prefered format to use in combination with PHP template engines. 
 */
define('AFS_ARRAY_FORMAT', 2);
/** @} */

/** @brief Main helper for AFS search reply.
 *
 * This helper is intended to be initiliazed with the reply provided by @a 
 * AfsSearchQueryManager::send. It allows to manage replies of the first 
 * replyset, including facets and pager. Connection and query errors are
 * managed in a uniform way to simplify integration.
 */
class AfsResponseHelper extends AfsHelperBase
{
    private $replyset = null;
    private $error = null;

    /** @brief Construct new response helper instance.
     *
     * @param $response [in] result from @a AfsSearchQueryManager::send call.
     * @param $facet_mgr [in] @a AfsFacetManager used to create appropriate
     *        queries.
     * @param $query [in] query which has produced current reply.
     * @param $coder [in] @a AfsQueryCoderInterface if set it will be used to
     *        create links (default: null).
     * @param $format [in] if set to AFS_ARRAY_FORMAT (default), all underlying
     *        helpers will be formatted as array of data, otherwise they are
     *        kept as is. See @ref helper_format for more details.
     * @param $visitor [in] text visitor implementing @a AfsTextVisitorInterface
     *        used to extract title and abstract contents. If not set, default
     *        visitor is used (see @a AfsReplyHelper).
     *
     * @exception InvalidArgumentException when one of the parameters is 
     * invalid.
     */
    public function __construct($response, AfsFacetManager $facet_mgr,
        AfsQuery $query, AfsQueryCoderInterface $coder=null,
        $format=AFS_ARRAY_FORMAT, AfsTextVisitorInterface $visitor=null)
    {
        $this->check_format($format);
        if (property_exists($response, 'replySet')) {
            $replyset_helper = new AfsReplysetHelper($response->replySet[0],
                $facet_mgr, $query, $coder, $format, $visitor);
            $this->replyset = $format == AFS_ARRAY_FORMAT ? $replyset_helper->format()
                                 : $replyset_helper;
        } elseif (property_exists($response->header, 'error')) {
            $this->error = $response->header->error->message[0];
        } else {
            $this->error = 'Unmanaged error';
        }
    }

    /** @brief Check whether reponse has a reply.
     * @return true when a reply is available, false otherwise.
     */
    public function has_replyset()
    {
        return ! is_null($this->replyset);
    }

    /** @brief Retrieve first replyset from the @a response.
     * @return @a AfsReplysetHelper or formatted replyset depending on @a format
     * parameter.
     */
    public function get_replyset()
    {
        return $this->replyset;
    }

    /** @brief Check whether an error has been raised.
     * @return True on error, false otherwise.
     */
    public function in_error()
    {
        return ! is_null($this->error);
    }

    /** @brief Retrieve error message.
     * @return Error message.
     */
    public function get_error_msg()
    {
        return $this->error;
    }
}

?>
