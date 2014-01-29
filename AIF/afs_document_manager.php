<?php
require_once "AIF/afs_document.php";

/** @brief Document manager.
 *
 * Manage one or more documents to be sent to through Antidot Back Office APIs. */
class AfsDocumentManager
{
    private $documents = array();

    /** @brief Add new document to the manager.
     *
     * @remark The provided @a id can be used later to remove the document from
     * the list of managed documents.
     * @remark Any existing document with same @a id is replaced by new one.
     *
     * @param $doc [in] new document to be managed (see @a AfsDocument).
     * @param $id [in] identifier associated to the document (default: null).
     *
     * @exception InvalidArgumentException when provided document is invalid.
     */
    public function add_document(AfsDocument $doc, $id=null)
    {
        if (! $doc->is_valid()) {
            throw new InvalidArgumentException('Trying to add invalid document');
        }
        if (is_null($id)) {
            $this->documents[] = $doc;
        } else {
            $this->documents[$id] = $doc;
        }
    }

    /** @brief Check whether at least one document is registered.
     * @return true when one or more documents is managed, false otherwise.
     */
    public function has_document()
    {
        return count($this->documents) > 0;
    }

    /** @brief Retrieve all managed documents.
     * @return all documents.
     */
    public function get_documents()
    {
        return $this->documents;
    }
}


