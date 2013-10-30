package net.antidot.api.search;

import static org.junit.Assert.*;

import org.junit.Test;

public class ReplyHelperTest extends ProtobufLoader {

	@Test
	public void testUriTitleAbstractWithoutMatch() {
		ReplyHelper helper = new ReplyHelper(loadProtobuf("good_reply_with_facets.protobuf").getReplySet(0).getContent().getReply(0));
		
		assertEquals("file://localhost//home/ml/travail/index/test_php/BO/upload/WS//1930e48d-d91e-467f-8a8a-834923d9c905/paf.log", helper.getUri());
		assertEquals("09:07:38 [I:5] Watching directory /usr/local/afs7/conf/", helper.getTitle());
		assertEquals("09:07:38 [I:4] Load configuration from /usr/local/afs7/conf/conf.xml 09:07:38 [I:4] Load configuration from /home/ml/travail/DEV/core/tests/PaF/old/cmd/test-predsucc/conf/conf.xml 09:07:38 [I:6] T__manager::initialize(SAME_PaFId) 09:07:38 [I:5] Enabling PaF Wal option 09:07:38 [I:5] Enabling PRAGMA…", helper.getAbstract());
	}

	@Test
	public void testTitleMatch() {
		ReplyHelper helper = new ReplyHelper(loadProtobuf("good_reply_with_highlight.protobuf").getReplySet(0).getContent().getReply(0));
		
		assertEquals("file://localhost//home/ml/travail/index/test_php/BO/upload/WS//ac7e367e-9237-41fd-8479-5d6b001f73c9/file", helper.getUri());
		assertEquals("<strong>FOO</strong> BAR BAZ", helper.getTitle());  // Default formatter only adds <em> tag
	}
	
	@Test
	public void testTitleMatchWithSpecificFormatter() {
		class MyFormatter implements TextFormatter {

			@Override
			public String text(String input) {
				return "[[[" + input + "]]]";
			}

			@Override
			public String match(String input) {
				return "<<<" + input + ">>>";
			}

			@Override
			public String trunc() {
				return "";
			}
		}
		
		ReplyHelper helper = new ReplyHelper(loadProtobuf("good_reply_with_highlight.protobuf").getReplySet(0).getContent().getReply(0));
		
		assertEquals("file://localhost//home/ml/travail/index/test_php/BO/upload/WS//ac7e367e-9237-41fd-8479-5d6b001f73c9/file", helper.getUri());
		assertEquals("<<<FOO>>>[[[ BAR BAZ]]]", helper.getTitle(new MyFormatter()));  // Default formatter does nothing
	}
	
	@Test
	public void testSpecificTruncFormatter() {
		class MyFormatter implements TextFormatter {

			@Override
			public String text(String input) {
				return input;
			}

			@Override
			public String match(String input) {
				return input;
			}

			@Override
			public String trunc() {
				return "[PREMATURE END]";
			}
		}
		
		ReplyHelper helper = new ReplyHelper(loadProtobuf("good_reply_with_facets.protobuf").getReplySet(0).getContent().getReply(0));
		
		assertEquals("file://localhost//home/ml/travail/index/test_php/BO/upload/WS//1930e48d-d91e-467f-8a8a-834923d9c905/paf.log", helper.getUri());
		assertEquals("09:07:38 [I:4] Load configuration from /usr/local/afs7/conf/conf.xml 09:07:38 [I:4] Load configuration from /home/ml/travail/DEV/core/tests/PaF/old/cmd/test-predsucc/conf/conf.xml 09:07:38 [I:6] T__manager::initialize(SAME_PaFId) 09:07:38 [I:5] Enabling PaF Wal option 09:07:38 [I:5] Enabling PRAGMA[PREMATURE END]", helper.getAbstract(new MyFormatter()));
	}
}
