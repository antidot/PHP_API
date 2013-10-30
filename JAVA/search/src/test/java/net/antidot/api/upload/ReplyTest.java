package net.antidot.api.upload;

import static org.junit.Assert.*;

import java.io.IOException;

import org.junit.Test;

public class ReplyTest {
	@Test
	public void testGoodReply() {
		String json = "{"
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
			+"}";
		
		Reply reply = null;
		try {
			reply = Reply.createReply(json);
		} catch (IOException e) {
			fail("Creting reply should not have failed: " + e);
		}
		
		assertEquals(reply.getJobId(), 90);
		assertEquals(reply.getUuid().toString(), "1930e48d-d91e-467f-8a8a-834923d9c905");
		assertEquals(reply.isStarted(), true);
	}

	@Test
	public void testBadReply() {
		String json = "{"
				+    "\"error\": {"
				+        "\"code\": 404," 
				+        "\"description\": \"Service 42 has no instance with SANDBOX status\"," 
				+        "\"details\": \"Not Found\","
				+        "\"id\": \"404\"," 
				+        "\"message\": \"Not Found\"," 
				+        "\"specURI\": \"http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.5\"," 
				+        "\"x:type\": \"ws.information.status\""
				+    "}," 
				+    "\"query\": {"
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
				+    "\"x:type\": \"ws.response\""
				+"}";
		
		try {
			Reply.createReply(json);
		} catch (FileUploadException e) {
			assertEquals(e.getErrorCode(), 404);
			assertEquals(e.getMessage(), "Service 42 has no instance with SANDBOX status");
			assertEquals(e.getDetails(), "Not Found");
			return;
		} catch (IOException e) {
			fail("Creting reply should not have failed like this : " + e);
		}
		
		fail("Reply creation should have failed!");
	}

}