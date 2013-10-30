package net.antidot.api.upload;

import java.io.IOException;


/** Interface for document visitors.
 */
public interface DocumentVisitorInterface {
	/** Works on {@link FileDocument}.
	 * @param doc [in] document to work on.
	 * @throws IOException underlying file is invalid or cannot be reached.
	 */
	public void visit(FileDocument doc) throws IOException;
	/** Works on {@link TextDocument}.
	 * @param doc [in] document to work on.
	 * @throws IOException error occurred while transforming text content (bad encoding).
	 */
	public void visit(TextDocument doc) throws IOException;
}
