package net.antidot.api.upload;

import java.io.IOException;

import org.apache.http.entity.ContentType;

/** Interface for managed documents.
 */
public interface DocumentInterface {
	/** Retrieves filename of the document.
	 * <p>
	 * This name is used to store the document on PaF server side.
	 * @return base file name of the document.
	 */
	public String getFilename();
	/** Retrieves document content type.
	 * @return content type of the document.
	 */
	public ContentType getContentType();
	/** Retrieves document content length.
	 * @return content length of the document.
	 */
	public long getContentLength();

	/** Accepts document visitor.
	 * @param visitor [in] visitor to use to work on document.
	 * @throws IOException document cannot be read from file system or other errors. 
	 */
	public void accept(DocumentVisitorInterface visitor) throws IOException;
}
