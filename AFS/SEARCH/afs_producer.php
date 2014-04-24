<?php
require_once "COMMON/afs_tools.php";

/** @brief Defines AFS search replies' producers.
 *
 * Specifies which agent produces reply set.
 */
class AfsProducer extends BasicEnum
{
	private static $instance = null;

  static public function check_value($value, $msg=null)
  {
      if (is_null(self::$instance))
          self::$instance = new self();
      BasicEnum::check_val(self::$instance, $value, $msg);
  }

 /** @brief Reply set result of check query. */
  const CHECK = 'CHECK';
  /** @brief Reply set result of search agent. */
  const SEARCH = 'SEARCH';
  /** @brief Reply set result of spellcheck agent. */
  const SPELLCHECK = 'SPELLCHECK';
  /** @brief Reply set result of concept agent. */
  const CONCEPT = 'CONCEPT';
  /** @brief Reply set result of proxy. */
  const PROXY = 'PROXY';
  /** @brief Reply set result of master agent. */
  const SEARCH_MASTER = 'SEARCH_MASTER';
  /** @brief Reply set result of slave agent. */
  const SEARCH_SLAVE = 'SEARCH_SLAVE';
}


