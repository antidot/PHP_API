package net.antidot.api.search;

import static org.junit.Assert.*;

import java.io.IOException;
import java.util.Collection;
import java.util.Map;
import java.util.Map.Entry;

import org.junit.Before;
import org.junit.Test;

public class QueryManagerTest {

	private ConnectorInterface connector;

	@Before
	public void setup() {
		connector = new ConnectorInterface() {
			
			@Override
			public byte[] send(Map<String, Collection<String>> params)
					throws IOException {
				StringBuilder builder = new StringBuilder();
				for (Entry<String, Collection<String>> entry : params.entrySet()) {
					builder.append(entry.getKey()).append("=");
					for (String value : entry.getValue()) {
						builder.append(value).append(" ");
					}
					builder.append(" - ");
				}
				return builder.toString().getBytes();
			}
		};
	}
	
	@Test
	public void testEmptyQuery() {
		FacetRegistry facetRegistry = new FacetRegistry();
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create();
		
		try {
			assertEquals("afs:replies=10  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
	
	@Test
	public void testWithPageNumber() {
		FacetRegistry facetRegistry = new FacetRegistry();
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().setPage(42L);
		
		try {
			assertEquals("afs:page=42  - afs:replies=10  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
	
	@Test
	public void testWithFeed() {
		FacetRegistry facetRegistry = new FacetRegistry();
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().setFeed("foo");
		
		try {
			assertEquals("afs:feed=foo  - afs:replies=10  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
	
	@Test
	public void testWithRepliesNumber() {
		FacetRegistry facetRegistry = new FacetRegistry();
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().setReplies(42);
		
		try {
			assertEquals("afs:replies=42  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
	
	@Test
	public void testWithLanguage() {
		FacetRegistry facetRegistry = new FacetRegistry();
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().setLanguage("es-ES");
		
		try {
			assertEquals("afs:lang=es-ES  - afs:replies=10  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
	
	@Test
	public void testWithQuery() {
		FacetRegistry facetRegistry = new FacetRegistry();
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().setSearchString("foo");
		
		try {
			assertEquals("afs:query=foo  - afs:replies=10  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
	
	@Test
	public void testWithSort() {
		FacetRegistry facetRegistry = new FacetRegistry();
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().setSort("afs:foo");
		
		try {
			assertEquals("afs:replies=10  - afs:sort=afs:foo  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
	
	@Test
	public void testWithFilterAnd() {
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet("foo", FacetType.INTEGER, FacetMode.ADD, FacetCombination.AND, FacetStickyness.NON_STICKY));
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().setFilter("foo", "42").addFilter("foo", "666");
		
		try {
			assertEquals("afs:facetOrder=foo  - afs:filter=foo=42 and foo=666  - afs:replies=10  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
	
	@Test
	public void testWithFilterOr() {
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet("foo", FacetType.INTEGER, FacetMode.ADD, FacetCombination.OR, FacetStickyness.NON_STICKY));
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().addFilter("foo", "bar").addFilter("foo", "baz");
		
		try {
			assertEquals("afs:facetOrder=foo  - afs:filter=foo=bar or foo=baz  - afs:replies=10  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
	
	@Test
	public void testWithFilterStickyness() {
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet("foo", FacetType.INTEGER, FacetMode.ADD, FacetCombination.OR, FacetStickyness.STICKY));
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().addFilter("foo", "bar");
		
		try {
			assertEquals("afs:facet=foo,sticky=true  - afs:facetOrder=foo  - afs:filter=foo=bar  - afs:replies=10  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
		
	@Test
	public void testFacetOrder() {
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet("foo", FacetType.INTEGER, FacetMode.ADD, FacetCombination.OR, FacetStickyness.NON_STICKY));
		facetRegistry.addFacet(new Facet("aoz", FacetType.INTEGER, FacetMode.ADD, FacetCombination.OR, FacetStickyness.NON_STICKY));
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().addFilter("foo", "bar").addFilter("aoz", "baz");
		
		try {
			assertEquals("afs:facetOrder=foo,aoz  - afs:filter=aoz=baz foo=bar  - afs:replies=10  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
		
	@Test
	public void testStringFacet() {
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet("foo", FacetType.STRING, FacetMode.ADD, FacetCombination.AND, FacetStickyness.NON_STICKY));
		QueryManager qm = new QueryManager(connector, facetRegistry);
		Query query = Query.create().addFilter("foo", "bar");
		
		try {
			assertEquals("afs:facetOrder=foo  - afs:filter=foo=\"bar\"  - afs:replies=10  - ", new String(qm.send(query)));
		} catch (IOException e) {
			fail("Should not have fail with: " + e.getMessage());
		}
	}
}
