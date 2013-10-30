package net.antidot.api.upload;

import java.io.IOException;

import org.apache.http.entity.ContentType;

import junit.framework.TestCase;

public class TextDocumentTest extends TestCase {

	public void testTextDocumentString() {
		String input = "foo";
		TextDocument doc = new TextDocument(input);

		assertNotNull(doc.getFilename());
		assertFalse("filename should not be empty", doc.getFilename().isEmpty());
		assertEquals(input, doc.getData());
		assertEquals(input.length(), doc.getContentLength());
		assertEquals(ContentType.APPLICATION_OCTET_STREAM, doc.getContentType());
	}

	public void testTextDocumentStringContentType() {
		String input = "foo";
		ContentType type = ContentType.APPLICATION_ATOM_XML;
		TextDocument doc = new TextDocument(input, type);

		assertNotNull(doc.getFilename());
		assertFalse("filename should not be empty", doc.getFilename().isEmpty());
		assertEquals(input, doc.getData());
		assertEquals(input.length(), doc.getContentLength());
		assertEquals(type, doc.getContentType());
	}

	public void testTextDocumentStringString() {
		String input = "foo";
		String filename = "filename";
		TextDocument doc = new TextDocument(filename, input);

		assertEquals(filename, doc.getFilename());
		assertEquals(input, doc.getData());
		assertEquals(input.length(), doc.getContentLength());
		assertEquals(ContentType.APPLICATION_OCTET_STREAM, doc.getContentType());
	}

	public void testTextDocumentStringStringContentType() {
		String input = "foo";
		String filename = "filename";
		ContentType type = ContentType.APPLICATION_ATOM_XML;
		TextDocument doc = new TextDocument(filename, input, type);

		assertEquals(filename, doc.getFilename());
		assertEquals(input, doc.getData());
		assertEquals(input.length(), doc.getContentLength());
		assertEquals(type, doc.getContentType());
	}

	public void testAccept() {
		class TestVisitor implements DocumentVisitorInterface {
			private String content;

			public void visit(FileDocument doc) throws IOException {
				fail("Visiting unmanaged document!");
			}

			public void visit(TextDocument doc) throws IOException {
				this.content = doc.getData();
			}
			
			public String getContent() {
				return this.content;
			}
		}
		
		String input = "bar";
		TextDocument doc = new TextDocument(input);
		TestVisitor visitor = new TestVisitor();
		try {
			doc.accept(visitor);
		} catch (IOException e) {
			fail("Visit generates exception: " + e.getMessage());
		}
		
		assertEquals(input, visitor.getContent());
	}
}
