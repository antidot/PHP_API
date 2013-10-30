package net.antidot.api.search;

import static org.junit.Assert.*;

import org.junit.Test;

public class FacetTest {

	@Test
	public void testSimpleFacet() {
		String facetId = "foo";
		FacetType facetType = FacetType.BOOL;
		FacetMode facetMode = FacetMode.REPLACE;
		FacetCombination facetCombination = FacetCombination.OR;
		Facet facet = new Facet(facetId, facetType);
		
		assertEquals(facetId, facet.getId());
		assertEquals(facetType, facet.getType());
		assertEquals(facetMode, facet.getMode());
		assertTrue(facet.isReplaceMode());
		assertFalse(facet.isAddMode());
		assertEquals(facetCombination, facet.getCombination());
		assertTrue(facet.isSticky());
	}

	@Test
	public void testFacetWithMode() {
		String facetId = "foo";
		FacetType facetType = FacetType.BOOL;
		FacetMode facetMode = FacetMode.ADD;
		FacetCombination facetCombination = FacetCombination.OR;
		Facet facet = new Facet(facetId, facetType, facetMode);
		
		assertEquals(facetId, facet.getId());
		assertEquals(facetType, facet.getType());
		assertEquals(facetMode, facet.getMode());
		assertFalse(facet.isReplaceMode());
		assertTrue(facet.isAddMode());
		assertEquals(facetCombination, facet.getCombination());
		assertTrue(facet.isSticky());
	}

	@Test
	public void testFacetWithModeAndCombination() {
		String facetId = "foo";
		FacetType facetType = FacetType.BOOL;
		FacetMode facetMode = FacetMode.REPLACE;
		FacetCombination facetCombination = FacetCombination.AND;
		Facet facet = new Facet(facetId, facetType, facetMode, facetCombination);
		
		assertEquals(facetId, facet.getId());
		assertEquals(facetType, facet.getType());
		assertEquals(facetMode, facet.getMode());
		assertTrue(facet.isReplaceMode());
		assertFalse(facet.isAddMode());
		assertEquals(facetCombination, facet.getCombination());
		assertFalse(facet.isSticky());
	}

	@Test
	public void testFacetModeCombinationAndSticky() {
		String facetId = "foo";
		FacetType facetType = FacetType.BOOL;
		FacetMode facetMode = FacetMode.ADD;
		FacetCombination facetCombination = FacetCombination.OR;
		FacetStickyness stickyness = FacetStickyness.NON_STICKY;
		Facet facet = new Facet(facetId, facetType, facetMode, facetCombination, stickyness);
		
		assertEquals(facetId, facet.getId());
		assertEquals(facetType, facet.getType());
		assertEquals(facetMode, facet.getMode());
		assertFalse(facet.isReplaceMode());
		assertTrue(facet.isAddMode());
		assertEquals(facetCombination, facet.getCombination());
		assertFalse(facet.isSticky());
	}

	@Test
	public void testBooleanFacetFormat() {
		Facet facet = new Facet("foo", FacetType.BOOL);
		assertEquals("true", facet.formatValue("true"));
	}

	@Test
	public void testIntegerFacetFormat() {
		Facet facet = new Facet("foo", FacetType.INTEGER);
		assertEquals("42", facet.formatValue("42"));
	}

	@Test
	public void testRealFacetFormat() {
		Facet facet = new Facet("foo", FacetType.REAL);
		assertEquals("42.666", facet.formatValue("42.666"));
	}

	@Test
	public void testDateFacetFormat() {
		Facet facet = new Facet("foo", FacetType.DATE);
		assertEquals("\"blabla\"", facet.formatValue("blabla"));
	}

	@Test
	public void testStringFacetFormat() {
		Facet facet = new Facet("foo", FacetType.STRING);
		assertEquals("\"true\"", facet.formatValue("true"));
	}

	@Test
	public void testIntervalFacetFormat() {
		Facet facet = new Facet("foo", FacetType.INTERVAL);
		assertEquals("]bar .. baz[", facet.formatValue("]bar .. baz["));
	}

}
