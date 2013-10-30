package net.antidot.api.upload;

import java.io.IOException;
import java.util.UUID;

import org.apache.http.entity.ContentType;

/** Text document
 * <p>
 * Document wrapper around generated text to be sent as a file to Antidot Back Office. 
 */
public class TextDocument implements DocumentInterface {
	private String filename;
	private String content;
	private ContentType contentType;

	/** Constructs text document.
	 * <p>
	 * Content type of the document is set to {@link ContentType#APPLICATION_OCTET_STREAM} by default.
	 * <br/>Filename is automatically generated to store the document on PaF side.
	 * @param content [in] document content.
	 */
	public TextDocument(String content) {
		this(content, ContentType.APPLICATION_OCTET_STREAM);
	}
	
	/** Constructs text document with specific content type.
	 * <p>
	 * It is highly discouraged to use enumerated values from {@link ContentType} for textual content such as XML.
	 * In fact, ContentType.APPLICATION_XML force charset to ISO-8859-1.
	 * @param content [in] document content.
	 * @param contentType [in] content type of the document.
	 */
	public TextDocument(String content, ContentType contentType) {
		this(UUID.randomUUID().toString(), content, contentType);
	}

	/** Constructs document with specific file name.
	 * <p>
	 * Content type of the document is set to {@link ContentType#APPLICATION_OCTET_STREAM} by default.
	 * <br/>Provided <tt>filename</tt> is used to stored the document on PaF side.
	 * @param filename [in] storage name of the document.
	 * @param content [in] document content.
	 */
	public TextDocument(String filename, String content) {
		this(filename, content, ContentType.APPLICATION_OCTET_STREAM);
	}

	/** Constructs document with specific filename and content type.
	 * <p>
	 * Content type of the document is set to {@link ContentType#APPLICATION_OCTET_STREAM} by default.
	 * <p>
	 * It is highly discouraged to use enumerated values from {@link ContentType} for textual content such as XML.
	 * In fact, ContentType.APPLICATION_XML force charset to ISO-8859-1.
	 * @param filename [in] storage name of the document.
	 * @param content [in] document content.
	 * @param contentType [in] content type of the document.
	 */
	public TextDocument(String filename, String content, ContentType contentType) {
		this.filename = filename;
		this.content = content;
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
		return this.filename;
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.upload.DocumentInterface#getContentType()
	 */
	public ContentType getContentType() {
		return this.contentType;
	}
	
	/** Retrieves data of this document.
	 * @return string content of the document.
	 */
	public String getData() {
		return this.content;
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.upload.DocumentInterface#getContentLength()
	 */
	public long getContentLength() {
		return this.content.length();
	}
}
