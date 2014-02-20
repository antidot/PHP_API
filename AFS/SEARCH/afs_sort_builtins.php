<?php
require_once 'COMMON/afs_tools.php';

/** @brief List of built-ins sort parameters. */
class AfsSortBuiltins extends BasicEnum
{
    /** @brief Number of the query words found in the document. */
    const WORDS = 'afs:words';
    /** @brief Minimal distance between query words in the document. */
    const PATHLEN = 'afs:pathlen';
    /** @brief Importance of the words in the document between 1 and 100. */
    const WEIGHT = 'afs:weight';
    /** @brief Criterion allows replies containing exactly the keywords in an
     * item to be more relevant. */
    const FIELDMATCH = 'afs:fieldMatch';
    /** @brief Shortcut for <tt>afs:words,DESC</tt> and
     * <tt>afs:fieldMatch,DESC</tt> and <tt>afs:pathlen,ASC</tt> and
     * <tt>afs:weight,DESC</tt>. */
    const RELEVANCE = 'afs:relevance';
    /** @brief DocId of the document. */
    const DOCID = 'afs:docId';
    /** @brief URI of the document. */
    const URI = 'afs:uri';
    /** @brief Langauge of the document. */
    const LANG = 'afs:lang';
    /** @brief Size of the document. */
    const SIZE = 'afs:size';
    /** @brief Type of the document. */
    const DOCTYPE = 'afs:doctype';
}
