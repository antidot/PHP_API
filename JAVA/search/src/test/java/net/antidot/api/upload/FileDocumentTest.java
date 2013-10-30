package net.antidot.api.upload;

import static org.junit.Assert.*;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;

import org.apache.commons.io.FileUtils;
import org.apache.http.entity.ContentType;
import org.junit.AfterClass;
import org.junit.BeforeClass;
import org.junit.Test;

public class FileDocumentTest {

	private static String content;
	private static File file;

	@BeforeClass
	public static void createRefFile() throws IOException {
		content = "<?xml version=\"1.0\"><root/>";
		file = File.createTempFile("file_document_test", ".test.tmp");
		FileOutputStream out = new FileOutputStream(file);
		out.write(content.getBytes());
		out.close();
	}
	
	@AfterClass
	public static void deleteRefFile() {
		file.delete();
	}
	
	@Test
	public void testFileDocumentString() throws IOException {
		FileDocument doc = new FileDocument(file.getAbsolutePath());
		
		assertEquals(content, FileUtils.readFileToString(doc.getData()));
		assertEquals(content.length(), doc.getContentLength());
		assertEquals(ContentType.APPLICATION_OCTET_STREAM, doc.getContentType());
		assertEquals(file.getName(), doc.getFilename());
	}

	@Test
	public void testFileDocumentStringContentType() throws IOException {
		FileDocument doc = new FileDocument(file.getAbsolutePath(), ContentType.APPLICATION_XML);
		
		assertEquals(content, FileUtils.readFileToString(doc.getData()));
		assertEquals(content.length(), doc.getContentLength());
		assertEquals(ContentType.APPLICATION_XML, doc.getContentType());
		assertEquals(file.getName(), doc.getFilename());
	}

	@Test
	public void testAccept() {
		class TestVisitor implements DocumentVisitorInterface {
			private String content;

			public void visit(FileDocument doc) throws IOException {
				this.content = FileUtils.readFileToString(doc.getData());
			}

			public void visit(TextDocument doc) throws IOException {
				fail("Visiting unmanaged document!");
			}
			
			public String getContent() {
				return this.content;
			}
		}
		
		FileDocument doc = new FileDocument(file.getAbsolutePath());
		TestVisitor visitor = new TestVisitor();
		try {
			doc.accept(visitor);
		} catch (IOException e) {
			fail("Visit generates exception: " + e.getMessage());
		}
		
		assertEquals(content, visitor.getContent());
	}

}
