package net.antidot.api.upload;

import java.util.ArrayList;
import java.util.Iterator;

/** Document manager.
 * <p>
 * Manages various type of documents which implement {@link DocumentInterface}.
 */
public class DocumentManager {
	private ArrayList<DocumentInterface> docs = new ArrayList<DocumentInterface>();

	/** Adds new document to the list of managed documents.
	 * @param doc [in] new document to manage
	 * @return manager itself.
	 */
	public DocumentManager addDocument(DocumentInterface doc) {
		this.docs.add(doc);
		return this;
	}
	
	/** Checks whether at least one document is managed.
	 * @return true when at lest one document is managed, false otherwise.
	 */
	public boolean hasDocument() {
		return !docs.isEmpty();
	}
	
	/** Retrieves iterator on list of documents.
	 * @return document iterator.
	 */
	public Iterator<DocumentInterface> getDocumentIterator() {
		return this.docs.iterator();
	}
}
