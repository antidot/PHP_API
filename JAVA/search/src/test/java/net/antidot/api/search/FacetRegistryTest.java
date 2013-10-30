package net.antidot.api.search;

import static org.junit.Assert.*;

import java.util.Iterator;

import org.junit.Test;

public class FacetRegistryTest {

	@Test
	public void testAddOneFacet() {
		String facetId = "foo";
		FacetRegistry facetRegistry = new FacetRegistry();
		assertTrue(facetRegistry.getFacets().isEmpty());

		facetRegistry.addFacet(new Facet(facetId, FacetType.BOOL));
		assertFalse(facetRegistry.getFacets().isEmpty());
		assertEquals(1, facetRegistry.getFacets().size());

		Facet facet = facetRegistry.getFacet(facetId);
		assertEquals(facetId, facet.getId());
	}
	
	@Test
	public void testAddMultipleFacets() {
		String first = "foo";
		String second = "bar";
		String third = "blobla";
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet(first, FacetType.BOOL));
		facetRegistry.addFacet(new Facet(second, FacetType.BOOL));
		facetRegistry.addFacet(new Facet(third, FacetType.BOOL));

		assertFalse(facetRegistry.getFacets().isEmpty());
		assertEquals(3, facetRegistry.getFacets().size());
		Iterator<Facet> facetIt = facetRegistry.getFacets().iterator();
		assertEquals(first, facetIt.next().getId());
		assertEquals(second, facetIt.next().getId());
		assertEquals(third, facetIt.next().getId());

		assertEquals(first, facetRegistry.getFacet(first).getId());
		assertEquals(second, facetRegistry.getFacet(second).getId());
		assertEquals(third, facetRegistry.getFacet(third).getId());
	}

	@Test
	public void testAddFacetTwice() {
		String facetId = "foo";
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet(facetId, FacetType.BOOL));
		try {
			facetRegistry.addFacet(new Facet(facetId, FacetType.BOOL));
		} catch(IllegalArgumentException e) {
			return;
		}
		fail("Adding two facet with same id should have failed");
	}

	@Test
	public void testRetrieveUnknownFacetId() {
		String facetId = "foo";
		FacetRegistry facetRegistry = new FacetRegistry();
		facetRegistry.addFacet(new Facet(facetId, FacetType.BOOL));
		try {
			facetRegistry.getFacet("bar");
		} catch(IllegalArgumentException e) {
			return;
		}
		fail("Retrieving unknown facet should have failed");
	}
}
