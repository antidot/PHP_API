package net.antidot.api.search;

import static org.junit.Assert.*;

import java.util.Arrays;
import org.junit.Test;

public class PagerHelperTest extends ProtobufLoader{

	@Test
	public void testCurrentNextAndAllPages() {
		PagerHelper helper = new PagerHelper(loadProtobuf("good_reply_with_facets.protobuf").getReplySet(0).getPager(), null, null);
		
		assertFalse(helper.hasPrevious());
		assertTrue(helper.hasNext());
		assertEquals(2, helper.getNext());
		assertEquals(Arrays.asList(1L, 2L, 3L, 4L, 5L, 6L, 7L), helper.getPages());
		assertEquals(1, helper.getCurrent());
	}
	
	@Test
	public void testCurrentPreviousNextAndAllPages() {
		PagerHelper helper = new PagerHelper(loadProtobuf("good_reply_without_next_page.protobuf").getReplySet(0).getPager(), null, null);
		
		assertTrue(helper.hasPrevious());
		assertEquals(6, helper.getPrevious());
		assertFalse(helper.hasNext());
		assertEquals(Arrays.asList(1L, 2L, 3L, 4L, 5L, 6L, 7L), helper.getPages());
		assertEquals(7, helper.getCurrent());
	}

}
