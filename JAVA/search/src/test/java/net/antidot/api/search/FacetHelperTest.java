package net.antidot.api.search;

import static org.junit.Assert.*;

import java.io.UnsupportedEncodingException;
import java.net.URISyntaxException;
import java.net.URLEncoder;
import java.util.List;

import org.junit.Test;

public class FacetHelperTest extends ProtobufLoader {

	@Test
	public void testIntervalFacet() {
		String facetId = "ADVANCED_INTERVAL_DATE";
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet(facetId, FacetType.BOOL));
		FacetHelper helper = new FacetHelper(loadProtobuf("good_reply_with_facets.protobuf").getReplySet(0).getFacets().getFacet(0), facetRegistry, null, null);
		
		assertEquals(facetId, helper.getId());
		assertEquals("Advanced date interval", helper.getLabel());

		List<FacetValueHelperInterface> values = helper.getValues();
		assertEquals(5, values.size());
		assertEquals("[\"2009-11-01\" .. \"2013-10-31\"[", values.get(0).getKey());
		assertEquals(17, values.get(0).getCount());
	}

	@Test
	public void testTreeFacet() {
		String facetId = "TREE_DATE";
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet(facetId, FacetType.BOOL));
		FacetHelper helper = new FacetHelper(loadProtobuf("good_reply_with_facets.protobuf").getReplySet(0).getFacets().getFacet(9), facetRegistry, null, null);
		// see good_reply_with_facets.xml for reference
		
		assertEquals(facetId, helper.getId());
		assertEquals("Tree date", helper.getLabel());
		assertEquals(3, helper.getValues().size());

		FacetValueHelperInterface first = helper.getValues().get(2);
		assertEquals("2013", first.getKey());
		assertEquals(15, first.getCount());
		assertEquals("2013", first.getLabel());
		assertEquals(4, first.getValues().size());
	
		FacetValueHelperInterface second = first.getValues().get(1);
		assertEquals("2013-06", second.getKey());
		assertEquals(3, second.getCount());
		assertEquals("06", second.getLabel());
		assertEquals(2, second.getValues().size());
		
		FacetValueHelperInterface third = second.getValues().get(1);
		assertEquals("2013-06-21", third.getKey());
		assertEquals(1, third.getCount());
		assertEquals("21", third.getLabel());
		assertEquals(0, third.getValues().size());
	}
	
	@Test
	public void testLinkWithFacetValue() {
		String facetId = "TREE_DATE";
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet(facetId, FacetType.BOOL, FacetMode.REPLACE));
		Query query = Query.create();
		CoderManager coderMgr = new CoderManager();
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);
		FacetHelper helper = new FacetHelper(loadProtobuf("good_reply_with_facets.protobuf").getReplySet(0).getFacets().getFacet(9), facetRegistry, coder, query);
		
		try {
			assertTrue(helper.getLink("bar").contains("filter="  + URLEncoder.encode("TREE|_DATE_bar", "UTF-8")));
		} catch (UnsupportedEncodingException e) {
			fail("Failed to encode reference: " + e.getMessage());
		} catch (URISyntaxException e) {
			fail("Failed due to invalid URI: " + e.getMessage());
		}
	}
		
	@Test
	public void testLinkReplacingFacetValue() {
		String facetId = "TREE_DATE";
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet(facetId, FacetType.BOOL, FacetMode.REPLACE));
		Query query = Query.create().setFilter(facetId, "bar");
		CoderManager coderMgr = new CoderManager();
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);
		FacetHelper helper = new FacetHelper(loadProtobuf("good_reply_with_facets.protobuf").getReplySet(0).getFacets().getFacet(9), facetRegistry, coder, query);
		
		try {
			assertTrue(helper.getLink("baz").contains("filter="  + URLEncoder.encode("TREE|_DATE_baz", "UTF-8")));
		} catch (UnsupportedEncodingException e) {
			fail("Failed to encode reference: " + e.getMessage());
		} catch (URISyntaxException e) {
			fail("Failed due to invalid URI: " + e.getMessage());
		}
	}
		
	@Test
	public void testLinkAddingFacetValue() {
		String facetId = "TREE_DATE";
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet(facetId, FacetType.BOOL, FacetMode.ADD));
		Query query = Query.create().setFilter(facetId, "bar");
		CoderManager coderMgr = new CoderManager();
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);
		FacetHelper helper = new FacetHelper(loadProtobuf("good_reply_with_facets.protobuf").getReplySet(0).getFacets().getFacet(9), facetRegistry, coder, query);
		
		try {
			assertTrue(helper.getLink("baz").contains("filter="  + URLEncoder.encode("TREE|_DATE_bar_baz", "UTF-8")));
		} catch (UnsupportedEncodingException e) {
			fail("Failed to encode reference: " + e.getMessage());
		} catch (URISyntaxException e) {
			fail("Failed due to invalid URI: " + e.getMessage());
		}
	}
	
	@Test
	public void testFilterValueIsPresent() {
		String facetId = "TREE_DATE";
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet(facetId, FacetType.BOOL, FacetMode.ADD));
		Query query = Query.create().setFilter(facetId, "bar");
		CoderManager coderMgr = new CoderManager();
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);
		FacetHelper helper = new FacetHelper(loadProtobuf("good_reply_with_facets.protobuf").getReplySet(0).getFacets().getFacet(9), facetRegistry, coder, query);
		
		assertTrue(helper.isSet("bar"));
		assertFalse(helper.isSet("baz"));
	}
}
