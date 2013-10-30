package net.antidot.api.upload;

import java.io.File;
import java.io.IOException;

import org.apache.commons.io.FilenameUtils;
import org.apache.http.entity.ContentType;

/** File document.
 * <p>
 * Document wrapper around a file to be sent to Antidot Back Office.
 */
public class FileDocument implements DocumentInterface {
	private String filename;
	private ContentType contentType;

	/** Constructs new file document.
	 * <p>
	 * The instance is created with default content type set to APPLICATION_OCTET_STREAM.
	 * @param filename [in] full filename (path + filename) of the file.
	 */
	public FileDocument(String filename) {
		this(filename, ContentType.APPLICATION_OCTET_STREAM);
		// we can detect content type in simple way in Java 7: Files.probeContentType(path)
	}

	/** Constructs new file document with specific content type.
	 * <p>
	 * It is highly discouraged to use enumerated values from @see ContentType for textual content such as XML.
	 * In fact, ContentType.APPLICATION_XML force charset to ISO-8859-1.
	 * @param filename [in] full filename (path + filename) of the file.
	 * @param contentType [in] content type of the file.
	 */
	public FileDocument(String filename, ContentType contentType) {
		File file = new File(filename);
		if (file.isFile() != true) {
			throw new IllegalArgumentException(filename + " is not a valid file");
		}
		this.filename = filename;
		this.contentType = contentType;
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.upload.DocumentInterface#accept(net.antidot.api.upload.DocumentVisitorInterface)
	 */
	public void accept(DocumentVisitorInterface visitor) throws IOException {
		visitor.visit(this);
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.upload.DocumentInterface#getFilename()
	 */
	public String getFilename() {
		return FilenameUtils.getName(this.filename);
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.upload.DocumentInterface#getContentType()
	 */
	public ContentType getContentType() {
		return this.contentType;
	}
	
	/** Retrieves File object for this document.
	 * @return File object.
	 * @throws IOException provided filename is invalid or no more valid.
	 */
	public File getData() throws IOException {
		return new File(this.filename);
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.upload.DocumentInterface#getContentLength()
	 */
	public long getContentLength() {
		return new File(this.filename).length();
	}
}
