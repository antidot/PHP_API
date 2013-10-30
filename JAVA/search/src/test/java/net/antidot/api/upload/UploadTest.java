package net.antidot.api.upload;

import java.io.IOException;
import java.net.URISyntaxException;

import org.apache.http.ParseException;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.entity.ContentType;
import org.junit.Test;
import org.junit.runner.RunWith;

import static org.junit.Assert.*;
import static org.mockito.Mockito.*;

import org.mockito.runners.MockitoJUnitRunner;

import net.antidot.api.common.Authentication;
import net.antidot.api.common.Authorities;
import net.antidot.api.common.Service;

@RunWith(MockitoJUnitRunner.class)
public class UploadTest {
	@Test
	public void testUploadSuccess() {
		Connector connector = spy(new Connector("localhost", new Service(42), "MockitoTest", new Authentication("foo", "bar", Authorities.AFS_AUTH_ANTIDOT)));
		
		DocumentManager mgr = new DocumentManager();
		mgr.addDocument(new TextDocument("<?xml version=\"1.0\"?><root><uri>http://JAVAgenerated.doc.php</uri><title>JAVA BAR1 Generated doc</title><content>JAVA Generated content</content></root>", ContentType.create("application/xml", "")));
		
		Reply testReply = null;
		try {
			testReply = Reply.createReply("{"
			+     "\"query\": {"
			+        "\"locale\": \"*\","
			+        "\"parameters\": {"
			+            "\"x:type\": \"collection\"," 
			+            "\"x:values\": []"
			+        "},"
			+        "\"properties\": {"
			+            "\"x:type\": \"x:dynamic\""
			+        "},"
			+        "\"x:type\": \"ws.response.query\""
			+    "},"
			+    "\"result\": {"
			+        "\"jobId\": 90," 
			+        "\"started\": true," 
			+        "\"uuid\": \"1930e48d-d91e-467f-8a8a-834923d9c905\"," 
			+        "\"x:type\": \"PushPafContentReply\""
			+    "},"
			+    "\"x:type\": \"ws.response\""
			+"}");
		} catch (IOException e) {
			fail("Create reply should not have failed: " + e);
		}
		
		try {
			doReturn(testReply).when(connector).buildReply(any(CloseableHttpResponse.class));
		} catch (ParseException e) {
			fail("Should never fail while partial-mocking: " + e);
		} catch (IOException e) {
			fail("Should never fail while partial-mocking: " + e);
		}
		
		Reply reply = null;
		try {
			reply = connector.uploadDocuments(mgr);
		} catch (URISyntaxException e) {
			fail("Failed due to invalid URI: " + e.getMessage());
		} catch (IOException e) {
			fail("Error occured while uploading document: " + e);
		}
		
		assertEquals(reply.getJobId(), 90);
		assertEquals(reply.getUuid().toString(), "1930e48d-d91e-467f-8a8a-834923d9c905");
		assertEquals(reply.isStarted(), true);
	}
}
