<?php
require_once "afs_text_helper.php";

/** @brief Default implementation for text visitor.
 *
 * @include afs_text_visitor.php
 */
class AfsTextVisitor implements AfsTextVisitorInterface
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
     * @return text value associated to visited instance surrounded by
     *         <tt>&lt;b> ... &lt;/b></tt>.
     */
    public function visit_AfsMatchText(AfsMatchText $afs_text)
    {
        return '<b>' . $afs_text->get_text() . '</b>';
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
