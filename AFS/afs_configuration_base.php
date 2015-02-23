<?php
require_once 'COMMON/afs_helper_format.php';
require_once 'COMMON/afs_user_session_manager.php';

class TextEncoding extends BasicEnum {
    const UTF8 = 'UTF-8';
    const ISO88591 = 'ISO-8859-1';
}

/** @brief Configuration base class for AFS configuration classes. */
abstract class AfsConfigurationBase
{
    protected $helper_format = null;
    protected $user_session_mgr = null;
    protected $text_encoding = TextEncoding::UTF8;

    /** @brief Constructs new configuration class with default parameters set.
     */
    public function __construct()
    {
        $this->helper_format = AfsHelperFormat::HELPERS;
        $this->user_session_mgr = new AfsUserSessionManager();
    }

    /**
     * @Brief get the actual user encoding
     * @return the current encoding (UTF-8 or ISO-8859-1)
     */
    public function get_text_encoding() {
        return $this->text_encoding;
    }

    /**
     * @Brief set the user encoding for URL building and response parsing
     *        default used is UTF-8. UTF-8 and ISO-8859-1 are supported
     * @param TextEncoding [in] $encoding
     */
    public function set_text_encoding($encoding) {
        $this->text_encoding = $encoding;
    }

    /** @name Helper format
     * @{ */

    /** @brief Retrieves reply helper format.
     * @return helper format (AfsHelperFormat::ARRAYS or
     *         AfsHelperFormat::HELPERS (default))
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
        AfsHelperFormat::check_value($format, 'Invalid helper format: ');
        $this->helper_format = $format;
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


