package net.antidot.api.search;

import static org.junit.Assert.*;

import java.util.NoSuchElementException;

import net.antidot.protobuf.reply.Reply.replies;
import net.antidot.protobuf.reply.Reply.replies.Builder;

import org.junit.Test;

public class RepliesHelperTest extends ProtobufLoader {

	@Test
	public void testErrorReply() {
		String errorMsg = new String("Test ERROR");
		Builder repliesBuilder = replies.newBuilder();
		repliesBuilder.getHeaderBuilder().getErrorBuilder().addMessage(errorMsg).build();
		RepliesHelper helper = new RepliesHelper(repliesBuilder.build(), null, null, null);

		assertTrue(helper.isError());
		assertEquals(helper.getErrorMessage(), errorMsg);
		assertFalse(helper.hasReplySet());
	}

	@Test
	public void testNoErrorReply() {
		RepliesHelper helper = new RepliesHelper(loadProtobuf("good_reply_with_facets.protobuf"), null, null, null);
		
		assertFalse(helper.isError());
		assertEquals(helper.getErrorMessage(), "");
	}
	
	@Test
	public void testOneReplyAvailable() {
		FacetRegistry facetRegistry = new FacetRegistry();
		RepliesHelper helper = new RepliesHelper(loadProtobuf("good_reply_with_facets.protobuf"), facetRegistry, null, null);
		
		assertTrue(helper.hasReplySet());
		assertTrue(helper.hasNext());
		
		helper.next();
		try {
			helper.next();
		} catch (NoSuchElementException e) {
			return;
		}
		fail("Cannot call next twice when only one reply set is available");
	}
	
}
