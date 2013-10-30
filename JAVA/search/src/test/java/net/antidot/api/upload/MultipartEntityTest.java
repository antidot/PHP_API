package net.antidot.api.upload;

import static org.junit.Assert.*;

import java.io.ByteArrayOutputStream;
import java.io.IOException;

import net.antidot.api.common.NotImplementedException;

import org.junit.Before;
import org.junit.Test;

public class MultipartEntityTest {
	public class TestEntity extends MultipartEntity {

		public TestEntity(DocumentManager documentManager) {
			super(documentManager);
		}
		
		public String getBoundary() {
			return this.boundary;
		}
		
	}

	private String content;
	private TextDocument doc;
	private TestEntity entity;
	
	@Before
	public void setup() {
		content = "foo";
		doc = new TextDocument(content);
		DocumentManager mgr = new DocumentManager();
		mgr.addDocument(doc);
		entity = new TestEntity(mgr);
	}

	@Test
	public void testIsChunked() {
		assertFalse(entity.isChunked());
	}

	@Test (expected = NotImplementedException.class)
	public void testConsumeContent() throws IOException {
		entity.consumeContent();
	}

	@Test
	public void testGetContentType() {
		assertEquals("Content-Type: multipart/form-data; boundary=" + entity.getBoundary(), entity.getContentType().toString());
	}

	@Test
	public void testGetContentLength() {
		String boundary = entity.getBoundary();
		// '--' + boundary + '\r\n'
		// '\r\n' + '--' + boundary + '--' + '\r\n'
		long length = (2 + boundary.length() + 2) * 2 + 4;
		length += entity.getMultipartContentDisposition("1", doc.getFilename()).length();
		length += entity.getMultipartContentType(doc.getContentType().toString()).length();
		length += content.length();
		
		assertEquals(length, entity.getContentLength());
	}

	@Test
	public void testWriteTo() throws IOException {
		ByteArrayOutputStream out = new ByteArrayOutputStream();
		entity.writeTo(out);
		assertEquals(entity.getContentLength(), out.size());
		// well should we check the content ?
	}

	@Test
	public void testIsRepeatable() {
		assertEquals(false, entity.isRepeatable());
	}

	@Test
	public void testIsStreaming() {
		assertEquals(false, entity.isStreaming());
	}

	@Test
	public void testGetContentEncoding() {
		assertNull(entity.getContentEncoding());
	}

	@Test (expected = NotImplementedException.class)
	public void testGetContent() throws IllegalStateException, IOException {
		entity.getContent();
	}

}
