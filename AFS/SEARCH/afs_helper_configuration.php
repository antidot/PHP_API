<?php
require_once 'COMMON/afs_helper_format.php';
require_once 'AFS/SEARCH/afs_facet_manager.php';
require_once 'AFS/SEARCH/afs_query_coder.php';
require_once 'AFS/SEARCH/afs_text_visitor.php';
require_once 'AFS/SEARCH/afs_spellcheck_text_visitor.php';


/** @brief Configuration class for AFS helper classes. */
class AfsHelperConfiguration
{
    private $helper_format = null;
    private $facet_mgr = null;
    private $query_coder = null;
    private $reply_text_visitor = null;
    private $spellcheck_text_visitor = null;
    private $user_session_mgr = null;

    /** @brief Constructs new configuration class with default parameters set.
     */
    public function __construct()
    {
        $this->helper_format = AfsHelperFormat::ARRAYS;
        $this->facet_mgr = new AfsFacetManager();
        $this->reply_text_visitor = new AfsTextVisitor();
        $this->spellcheck_text_visitor = new AfsSpellcheckTextVisitor();
        $this->user_session_mgr = new AfsUserSessionManager();
    }

    /** @name Helper format
     * @{ */

    /** @brief Retrieves reply helper format.
     * @return helper format (AfsHelperFormat::ARRAYS or AfsHelperFormat::HELPERS)
     */
    public function get_helper_format()
    {
        return $this->helper_format;
    }
    /** @brief Checks whether helper format is set to ARRAYS.
     * @return @c True when helper format equals AfsHelperFormat::ARRAYS.
     */
    public function is_array_format()
    {
        return $this->helper_format == AfsHelperFormat::ARRAYS;
    }
    /** @brief Checks whether helper format is set to HELPERS.
     * @return @c True when helper format equals AfsHelperFormat::HELPERS.
     */
    public function is_helper_format()
    {
        return $this->helper_format == AfsHelperFormat::HELPERS;
    }
    /** @brief Defines new reply helper format.
     * @param $format [in] new format to set (see AfsHelperFormat)
     * @return current instance.
     * @exception InvalidArgumentException when provided format is invalid.
     */
    public function set_helper_format($format)
    {
        EnumChecker::check_value(AfsHelperFormat, $format, 'Invalid helper format: ');
        $this->helper_format = $format;
        return $this;
    }
    /** @} */

    /** @name Facet manager
     * @{ */

    /** @brief Retrieves facet manager.
     * @return facet manager (see AfsFacetManager).
     */
    public function get_facet_manager()
    {
        return $this->facet_mgr;
    }
    /** @brief Defines new facet manager.
     * @param $facet_mgr [in] new facet manager to set.
     * @return current instance.
     */
    public function set_facet_manager(AfsFacetManager $facet_mgr)
    {
        $this->facet_mgr = $facet_mgr;
        return $this;
    }
    /** @}*/

    /** @name Query coder
     * @{ */

    /** @brief Checks whether a query coder has been defined.
     * @return @c True when a query coder is defined, @c false otherwise.
     */
    public function has_query_coder()
    {
        if (is_null($this->query_coder)) {
            return false;
        } else {
            return true;
        }
    }
    /** @brief Retrieves query coder.
     * @return query coder (see AfsQueryCoderInterface).
     */
    public function get_query_coder()
    {
        return $this->query_coder;
    }
    /** @brief Defines new query coder.
     * @param $query_coder [in] new query coder to set.
     * @return current instance.
     */
    public function set_query_coder(AfsQueryCoderInterface $query_coder)
    {
        $this->query_coder = $query_coder;
        return $this;
    }
    /** @} */

    /** @name Reply text
     * @{ */

    /** @brief Retrieves reply text visitor.
     *
     * This visitor is used to format text for title and abstract replies.
     * @return reply text visitor (see AfsTextVisitorInterface).
     */
    public function get_reply_text_visitor()
    {
        return $this->reply_text_visitor;
    }

    /** @brief Defines new reply text visitor.
     * @param $visitor [in] new visitor to set
     * @return current instance.
     */
    public function set_reply_text_visitor(AfsTextVisitorInterface $visitor)
    {
        $this->reply_text_visitor = $visitor;
        return $this;
    }
    /** @} */

    /** @name Spellcheck text
     * @{ */

    /** @brief Retrieves spellcheck text visitor.
     * @return spellcheck text visitor (see AfsSpellcheckTextVisitorInterface).
     */
    public function get_spellcheck_text_visitor()
    {
        return $this->spellcheck_text_visitor;
    }

    /** @brief Defines new spellcheck text visitor.
     * @param $visitor [in] new visitor to set.
     * @return current instance.
     */
    public function set_spellcheck_text_visitor(AfsSpellcheckTextVisitorInterface $visitor)
    {
        $this->spellcheck_text_visitor = $visitor;
        return $this;
    }
    /** @} */

    /** @name User and Session identifiers
     * @{ */

    /** @brief Restrievs user and session id manager.
     * @return Manager for user and session id.
     */
    public function get_user_session_manager()
    {
        return $this->user_session_mgr;
    }
    /** @brief Defines new user and session id manager.
     * @param $user_session_mgr [in] new manager for user and session id.
     * @return current instance.
     */
    public function set_user_sessionmanager(AfsUserSessionManager $user_session_mgr)
    {
        $this->user_session_mgr = $user_session_mgr;
        return $this;
    }
    /** @} */
}


