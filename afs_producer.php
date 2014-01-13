<?php
require_once "afs_tools.php";

/** @brief Defines AFS search replies' producers.
 *
 * Specifies which agent produces reply set.
 */
abstract class AfsProducer extends BasicEnum
{
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

?>
