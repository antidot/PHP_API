<?php
require_once "AFS/SEARCH/afs_text_helper.php";

/** @brief Implementation for text visitor without any text formatting.
 */
class AfsRawTextVisitor implements AfsTextVisitorInterface
{
    /** @brief Visit @a AfsStringText instance.
     * @param $afs_text [in] visited instance.
     * @return text value associated to visited instance.
     */
    public function visit_AfsStringText(AfsStringText $afs_text)
    {
        return $afs_text->get_text();
    }

    /** @brief Visit @a AfsMatchText instance.
     * @param $afs_text [in] visited instance.
     * @return text value associated to visited instance.
     */
    public function visit_AfsMatchText(AfsMatchText $afs_text)
    {
        return $afs_text->get_text();
    }

    /** @brief Visit @a AfsTruncateText instance.
     * @param $afs_text [in] visited instance.
     * @return static value: <tt>...</tt>
     */
    public function visit_AfsTruncateText(AfsTruncateText $afs_text)
    {
        return '...';
    }
}

?>
