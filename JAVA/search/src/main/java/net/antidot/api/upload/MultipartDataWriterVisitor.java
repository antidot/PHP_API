package net.antidot.api.upload;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

import net.antidot.api.upload.DocumentVisitorInterface;
import net.antidot.api.upload.FileDocument;
import net.antidot.api.upload.TextDocument;

/** Document data writer.
 * <p>
 * Allows to visit various types of documents and use the best method to transfer each type of document.
 */
public class MultipartDataWriterVisitor implements DocumentVisitorInterface {

	private OutputStream outStream;
	private byte[] buffer;

	/** Constructs new visitor instance.
	 * <p>
	 * The instance is created with default buffer size of 2048 bytes.
	 * @param out [in-out] output stream to fill with document data.
	 */
	public MultipartDataWriterVisitor(OutputStream out) {
		this(out, 2048);
	}

	/** Constructs new visitor instance with specific buffer.
	 * @param out [in-out] output stream to fill with document data.
	 * @param bufferSize [in] size of the buffer used when working on {@link FileDocument}.
	 */
	public MultipartDataWriterVisitor(OutputStream out, int bufferSize) {
		this.outStream = out;
		this.buffer = new byte[bufferSize];
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.upload.DocumentVisitorInterface#visit(net.antidot.api.upload.FileDocument)
	 */
	public void visit(FileDocument doc) throws IOException {
		InputStream inStream = new FileInputStream(doc.getData());
		try {
			int readSize;
			while ((readSize = inStream.read(buffer)) != -1) {
				this.outStream.write(buffer, 0, readSize);
			}
		} finally {
			inStream.close();
		}
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.upload.DocumentVisitorInterface#visit(net.antidot.api.upload.TextDocument)
	 */
	public void visit(TextDocument doc) throws IOException {
		this.outStream.write(doc.getData().getBytes());
	}

}
