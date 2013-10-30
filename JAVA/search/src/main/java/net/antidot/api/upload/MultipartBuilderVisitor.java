package net.antidot.api.upload;

import java.io.ByteArrayInputStream;
import java.io.IOException;

import org.apache.http.entity.mime.MultipartEntityBuilder;

/** Visitor used to build multi-parts entities.
 */
public class MultipartBuilderVisitor implements DocumentVisitorInterface {

	private int fileId = 1;
	private MultipartEntityBuilder builder;

	/** Constructs new visitor.
	 * @param builder [in] multi-parts builder.
	 */
	public MultipartBuilderVisitor(MultipartEntityBuilder builder) {
		this.builder = builder;
	}
	
	/* (non-Javadoc)
	 * @see net.antidot.api.upload.DocumentVisitorInterface#visit(net.antidot.api.upload.FileDocument)
	 */
	public void visit(FileDocument doc) throws IOException {
		this.builder.addBinaryBody(this.getName(), doc.getData(), doc.getContentType(), doc.getFilename());
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.upload.DocumentVisitorInterface#visit(net.antidot.api.upload.TextDocument)
	 */
	public void visit(TextDocument doc) {
		ByteArrayInputStream bytes = new ByteArrayInputStream(doc.getData().getBytes());
		this.builder.addBinaryBody(this.getName(), bytes, doc.getContentType(), doc.getFilename());
	}
	
	/** Retrieves multi-parts parameter name. 
	 * @return unique parameter name.
	 */
	private String getName() {
		String result = "file" + fileId;
		fileId ++;
		return result;
	}

}
