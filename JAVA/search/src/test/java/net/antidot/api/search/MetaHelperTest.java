package net.antidot.api.search;

import static org.junit.Assert.*;
import net.antidot.protobuf.agent.Agent.Type;

import org.junit.Test;

public class MetaHelperTest extends ProtobufLoader {

	@Test
	public void testMetaData() {
		MetaHelper helper = new MetaHelper(loadProtobuf("good_reply_with_facets.protobuf").getReplySet(0).getMeta());
		
		assertEquals("TEST", helper.getFeedName());
		assertEquals(68, helper.getReplyNb());
		assertEquals(49, helper.getDuration());
		assertEquals(Type.SEARCH, helper.getProducer());
	}

}
