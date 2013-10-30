package net.antidot.api.upload;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.Iterator;

import net.antidot.api.common.NotImplementedException;
import net.antidot.api.upload.DocumentInterface;
import net.antidot.api.upload.DocumentManager;
import net.antidot.api.upload.DocumentVisitorInterface;
import net.antidot.api.upload.MultipartDataWriterVisitor;

import org.apache.http.Header;
import org.apache.http.entity.AbstractHttpEntity;
import org.apache.http.entity.ContentType;
import org.apache.http.message.BasicHeader;


/** Multi-parts entity.
 * <p>
 * Lighttpd (version 1.4.28 and 1.4.31) does not support chunked data.
 * So we provide our own multi-parts entity to send multi-parts data in one data block with known size. 
 */
public class MultipartEntity extends AbstractHttpEntity {

	private DocumentManager documentManager;
	private long contentLength = -1;
	protected String boundary;

	/** Constructs multi-parts entity from {@link DocumentManager}.
	 * @param documentManager [in] manager with at least one valid document.
	 * @throws IllegalArgumentException when document manager has no document.
	 */
	public MultipartEntity(DocumentManager documentManager) {
		this(documentManager, "AntidotMultiPart");
	}
	
	/** Constructs multi-parts entity from {@link DocumentManager} with specific boundary.
	 * @param documentManager [in] manager with at least one valid document.
	 * @param boundary [in] boundary used in multi-parts message.
	 * @throws IllegalArgumentException when document manager has no document.
	 * @throws IllegalArgumentException when provided boundary is empty or too lengthy.
	 */
	public MultipartEntity(DocumentManager documentManager, String boundary) {
		if (!documentManager.hasDocument()) {
			throw new IllegalArgumentException("At least one document should be managed.");
		} else if (boundary.isEmpty() || boundary.length() > 70) {
			throw new IllegalArgumentException("Multi-part boundary should not be empty and less than 70 characters");
		}
		this.documentManager = documentManager;
		this.boundary = boundary;
	}

	/* (non-Javadoc)
	 * @see org.apache.http.entity.AbstractHttpEntity#getContentType()
	 */
	public Header getContentType() {
		return new BasicHeader("Content-Type", "multipart/form-data; boundary=" + this.boundary);
	}

	/* (non-Javadoc)
	 * @see org.apache.http.HttpEntity#getContentLength()
	 */
	public long getContentLength() {
		if (this.contentLength == -1) {
			this.contentLength = this.getFirstBoundary().length;

			int parts = 0;
			Iterator<DocumentInterface> docIt = this.documentManager.getDocumentIterator();
			while (docIt.hasNext()) {
				parts ++;
				DocumentInterface doc = docIt.next();
				this.contentLength += doc.getContentLength();  // content
				this.contentLength += doc.getContentType().toString().length(); // Content-Type	
				this.contentLength += doc.getFilename().length(); // filename
				this.contentLength += Integer.toString(parts).length(); // part of name (fileXXXX)
			}

			long staticLength = this.getMultipartContentDispositionLength();
			staticLength += this.getMultipartContentTypeLength();
			this.contentLength += (staticLength + this.getIntermediateBoundary().length) * parts + 2;  // 2 for final boundary "--"
		}
		return this.contentLength;
	}

	/* (non-Javadoc)
	 * @see org.apache.http.HttpEntity#writeTo(java.io.OutputStream)
	 */
	public void writeTo(OutputStream out) throws IOException {
		int fileNo = 1;
		Iterator<DocumentInterface> docIt = this.documentManager.getDocumentIterator();
		DocumentVisitorInterface visitor = new MultipartDataWriterVisitor(out);
		while (docIt.hasNext()) {
			if (fileNo == 1) {
				out.write(this.getFirstBoundary());
			} else {
				out.write(this.getIntermediateBoundary());
			}
			DocumentInterface doc = docIt.next();
			out.write(this.getMultipartContentDisposition(fileNo, doc.getFilename()).getBytes());
			out.write(this.getMultipartContentType(doc.getContentType()).getBytes());
			doc.accept(visitor);
			fileNo ++;
		}
		out.write(this.getLastBoundary());
	}

	/* (non-Javadoc)
	 * @see org.apache.http.HttpEntity#isRepeatable()
	 */
	public boolean isRepeatable() {
		return false;
	}

	/* (non-Javadoc)
	 * @see org.apache.http.HttpEntity#isStreaming()
	 */
	public boolean isStreaming() {
		return false;
	}

	/* (non-Javadoc)
	 * @see org.apache.http.entity.AbstractHttpEntity#isChunked()
	 */
	public boolean isChunked() {
		return false;
	}

	/* (non-Javadoc)
	 * @see org.apache.http.entity.AbstractHttpEntity#getContentEncoding()
	 */
	public Header getContentEncoding() {
		// TODO Auto-generated method stub
		return null;
	}

	/* (non-Javadoc)
	 * @see org.apache.http.entity.AbstractHttpEntity#consumeContent()
	 */
	public void consumeContent() throws IOException {
		throw new NotImplementedException("Cannot consume content of multipart entity.");
	}

	/* (non-Javadoc)
	 * @see org.apache.http.HttpEntity#getContent()
	 */
	public InputStream getContent() throws IOException, IllegalStateException {
		throw new NotImplementedException("Cannot get content as InputStream for multipart entity.");
	}
	
	private byte[] getFirstBoundary() {
		return ("--" + this.boundary + "\r\n").getBytes();
	}
	
	private byte[] getIntermediateBoundary() {
		return ("\r\n--" + this.boundary + "\r\n").getBytes();
	}
	
	private byte[] getLastBoundary() {
		return ("\r\n--" + this.boundary + "--\r\n").getBytes();
	}
	
	/** @internal
	 * @brief Retrieves content disposition for a multi-parts.
	 * @param fileId [in] should start at 1 for the first uploaded file and incremented by 1 for each subsequent file.
	 * @param filename[in] storage name of the document on PaF side.
	 * @return a format string which should be used with {@link String#format}.
	 */
	private String getMultipartContentDisposition(int fileId, String filename) {
		return this.getMultipartContentDisposition(Integer.toString(fileId), filename);
	}

	private int getMultipartContentDispositionLength() {
		return this.getMultipartContentDisposition("", "").length();
	}

	protected String getMultipartContentDisposition(String fileId, String filename) {
		return String.format("Content-Disposition: form-data; name=\"file%s\"; filename=\"%s\"\r\n", fileId, filename);
	}
	
	private String getMultipartContentType(ContentType contentType) {
		return this.getMultipartContentType(contentType.toString());
	}
	
	private int getMultipartContentTypeLength() {
		return this.getMultipartContentType("").length();
	}
	
	protected String getMultipartContentType(String contentType) {
		return String.format("Content-Type: %s\r\n\r\n", contentType);
	}
}
