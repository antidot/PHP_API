package net.antidot.api.search;

import static org.junit.Assert.*;

import java.util.Map;
import java.util.TreeMap;
import java.util.TreeSet;

import net.antidot.common.lang.LangProtos.Lang;
import net.antidot.common.lang.RegionProtos.Region;
import net.antidot.protobuf.lang.Label.Language;

import org.junit.Test;

public class QueryTest {

	@Test
	public void testDefaultQuery() {
		Query query = Query.create();
		
		assertFalse(query.hasFeed());
		assertFalse(query.hasSearchString());
		assertTrue(query.getFilters().isEmpty());
		assertFalse(query.hasPage());
		assertTrue(query.hasReplies());
		assertFalse(query.hasLanguage());
		assertFalse(query.hasSort());
	}

	@Test
	public void testHasFeed() {
		String feed = "foo";
		Query query = Query.create().addFeed(feed);
		
		assertTrue(query.hasFeed(feed));
		assertFalse(query.hasFeed("bar"));
	}

	@Test
	public void testSetFeed() {
		String first = "foo";
		String second = "bar";
		Query query = Query.create().addFeed(first).setFeed(second);
		
		assertFalse(query.hasFeed(first));
		assertTrue(query.hasFeed(second));
	}

	@Test
	public void testAddFeed() {
		String first = "foo";
		String second = "bar";
		Query query = Query.create().addFeed(first).addFeed(second);
		
		assertTrue(query.hasFeed(first));
		assertTrue(query.hasFeed(second));
	}

	@Test
	public void testGetFeeds() {
		String feed = "foo";
		Query query = Query.create().addFeed(feed);
		
		TreeSet<String> expected = new TreeSet<String>();
		expected.add(feed);
		assertEquals(expected, query.getFeeds());
	}

	@Test
	public void testSetQuery() {
		String queryStr = "foo";
		Query query = Query.create().setSearchString(queryStr);
		
		assertTrue(query.hasSearchString());
		assertEquals(queryStr, query.getSearchString());
	}

	@Test
	public void testAddFilter() {
		String facetId = "foo";
		String value = "bar";
		Query query = Query.create().addFilter(facetId, value);
		
		assertFalse(query.getFilters().isEmpty());
		assertTrue(query.getFilters().containsKey(facetId));
		assertTrue(query.hasFilter(facetId, value));
		assertEquals(value, query.getFilterValues(facetId)[0]);
	}

	@Test
	public void testSetFilter() {
		String facetId = "foo";
		String first = "bar";
		String second = "baz";
		Query query = Query.create()
				.addFilter(facetId, first)
				.setFilter(facetId, second);
		
		assertFalse(query.getFilters().isEmpty());
		assertTrue(query.getFilters().containsKey(facetId));
		assertTrue(query.hasFilter(facetId, second));
		assertFalse(query.hasFilter(facetId, first));
	}


	@Test
	public void testRemoveFilterWithOneValue() {
		String facetId = "foo";
		String first = "bar";
		Query query = Query.create().addFilter(facetId, first)
				.removeFilter(facetId, first);
		
		assertTrue(query.getFilters().isEmpty());
		assertFalse(query.hasFilter(facetId, first));
	}

	@Test
	public void testRemoveFilterWithMultipleValue() {
		String facetId = "foo";
		String first = "bar";
		String second = "baz";
		Query query = Query.create().addFilter(facetId, first).addFilter(facetId, second)
				.removeFilter(facetId, first);
		
		assertFalse(query.getFilters().isEmpty());
		assertTrue(query.getFilters().containsKey(facetId));
		assertTrue(query.hasFilter(facetId, second));
		assertFalse(query.hasFilter(facetId, first));
	}

	@Test
	public void testGetFilterValues() {
		String facetId = "foo";
		String first = "bar";
		String second = "baz";
		Query query = Query.create().addFilter(facetId, first).addFilter(facetId, second);
		
		assertFalse(query.getFilters().isEmpty());
		assertTrue(query.getFilters().containsKey(facetId));
		assertTrue(query.hasFilter(facetId, first));
		assertTrue(query.hasFilter(facetId, second));
		String[] values = query.getFilterValues(facetId);
		assertEquals(first, values[0]);
		assertEquals(second, values[1]);
	}

	@Test
	public void testGetFilters() {
		String facetFirst = "foo";
		String facetSecond = "zoo";
		String first = "bar";
		String second = "baz";
		Query query = Query.create().addFilter(facetFirst, first).addFilter(facetSecond, second);
		
		assertTrue(query.hasFilter(facetFirst, first));
		assertFalse(query.hasFilter(facetFirst, second));
		assertFalse(query.hasFilter(facetSecond, first));
		assertTrue(query.hasFilter(facetSecond, second));
		assertFalse(query.getFilters().isEmpty());
		assertTrue(query.getFilters().containsKey(facetFirst));
		assertTrue(query.getFilters().containsKey(facetSecond));
		assertEquals(first, query.getFilters().get(facetFirst).iterator().next());
		assertEquals(second, query.getFilters().get(facetSecond).iterator().next());
	}

	@Test
	public void testHasPage() {
		Query query = Query.create();
		assertFalse(query.hasPage());
		
		query = query.setPage(42);
		assertTrue(query.hasPage());
	}

	@Test
	public void testGetPage() {
		Query query = Query.create();
		assertEquals(1, query.getPage());
		
		int pageNo = 666;
		query = query.setPage(pageNo);
		assertEquals(pageNo, query.getPage());
	}

	@Test
	public void testHasReplies() {
		Query query = Query.create();
		assertTrue(query.hasReplies());
	}

	@Test
	public void testGetReplies() {
		int repliesNb = 42666;
		Query query = Query.create().setReplies(repliesNb );
		
		assertEquals(repliesNb, query.getReplies());
	}

	@Test
	public void testHasLang() {
		Query query = Query.create();
		assertFalse(query.hasLanguage());
	}

	@Test
	public void testSetLanguage() {
		Language lang = Language.newBuilder()
				.setLang(Lang.AA).setRegion(Region.AC).build();
		Query query = Query.create().setLanguage(lang);

		assertTrue(query.hasLanguage());
		assertEquals("aa-AC", query.getLanguage());
	}

	@Test
	public void testSetLanguageLang() {
		Query query = Query.create().setLanguage(Lang.EN);

		assertTrue(query.hasLanguage());
		assertEquals("en", query.getLanguage());
	}

	@Test
	public void testSetLanguageLangRegion() {
		Query query = Query.create().setLanguage(Lang.ES, Region.ES);
		
		assertTrue(query.hasLanguage());
		assertEquals("es-ES", query.getLanguage());
	}

	@Test
	public void testSetLanguageWithLangCode() {
		Query query = Query.create().setLanguage("RU");
		
		assertTrue(query.hasLanguage());
		assertEquals("ru", query.getLanguage());
	}

	@Test
	public void testSetLanguageWithLangAndRegionCode() {
		Query query = Query.create().setLanguage("RUus");
		
		assertTrue(query.hasLanguage());
		assertEquals("ru-US", query.getLanguage());
		
		query = query.setLanguage("ru-us");
		assertEquals("ru-US", query.getLanguage());
		
		query = query.setLanguage("RU_US");
		assertEquals("ru-US", query.getLanguage());
	}
	
	@Test
	public void testSetLanguageBadLangCode() {
		Query query = Query.create();
		try {
			query.setLanguage("foo");
		} catch (IllegalArgumentException e) {
			return;
		}
		fail("Test should have launch an exception");
	}
	
	@Test
	public void testSetLanguageBadLangRegion() {
		Query query = Query.create();
		try {
			query.setLanguage("ru-foo");
		} catch (IllegalArgumentException e) {
			return;
		}
		fail("Test should have launch an exception");
	}

	@Test
	public void testGetRawLanguage() {
		Language lang = Language.newBuilder().setLang(Lang.AA).build();
		Query query = Query.create().setLanguage(lang);

		assertEquals(lang, query.getRawLanguage());
		
		lang = Language.newBuilder().setLang(Lang.IA).setRegion(Region.IC).build();
		query = query.setLanguage(lang);
		assertEquals(lang, query.getRawLanguage());
	}

	@Test
	public void testHasSort() {
		Query query = Query.create();
		assertFalse(query.hasSort());
	}

	@Test
	public void testSetSort() {
		Query query = Query.create().setSort("afs:relevance");
		assertTrue(query.hasSort());
	}
	
	@Test
	public void testSetSortMultiple() {
		Query query = Query.create().setSort("afs:relevance,ASC;afs:weight,DESC;afs:words");
		assertTrue(query.hasSort());
	}

	@Test
	public void testSetSortInvalidFormat() {
		try {
			Query.create().setSort("afs:relevance,ASC;afs:weight,DESC;afs:words,");
		} catch (IllegalArgumentException e) {
			return;
		}
		fail("Exception should have been raised.");
	}

	@Test
	public void testSetSortInvalidParam() {
		try {
			Query.create().setSort("foo");
		} catch (IllegalArgumentException e) {
			return;
		}
		fail("Exception should have been raised.");
	}

	@Test
	public void testSetSortInvalidParamOrder() {
		try {
			Query.create().setSort("afs:relevance,ASCENDING");
		} catch (IllegalArgumentException e) {
			return;
		}
		fail("Exception should have been raised.");
	}

	@Test
	public void testResetSort() {
		Query query = Query.create().setSort("afs:relevance").resetSort();
		assertFalse(query.hasSort());
	}

	@Test
	public void testGetSort() {
		String sort = "afs:relevance";
		Query query = Query.create().setSort(sort);
		
		assertTrue(query.hasSort());
		assertEquals(sort, query.getSort());
	}
	
	@Test
	public void testResetPageOnFeed() {
		Query query = Query.create().setPage(42);
		assertTrue(query.hasPage());
		query = query.setFeed("foo");
		assertFalse(query.hasPage());
	}
	
	@Test
	public void testResetPageOnFilter() {
		Query query = Query.create().setPage(42);
		assertTrue(query.hasPage());
		query = query.setFilter("foo", "bar");
		assertFalse(query.hasPage());
	}
	
	@Test
	public void testResetPageOnLanguage() {
		Query query = Query.create().setPage(42);
		assertTrue(query.hasPage());
		query = query.setLanguage("en");
		assertFalse(query.hasPage());
	}
	
	@Test
	public void testResetPageOnQuery() {
		Query query = Query.create().setPage(42);
		assertTrue(query.hasPage());
		query = query.setSearchString("foo");
		assertFalse(query.hasPage());
	}
	
	@Test
	public void testResetPageOnReplies() {
		Query query = Query.create().setPage(42);
		assertTrue(query.hasPage());
		query = query.setReplies(666);
		assertFalse(query.hasPage());
	}
	
	@Test
	public void testResetPageOnSort() {
		Query query = Query.create().setPage(42);
		assertTrue(query.hasPage());
		query = query.setSort("afs:relevance");
		assertFalse(query.hasPage());
	}
	
	@Test
	public void testCreateQueryFromQueryStringWithPage() {
		CoderManager coderMgr = new CoderManager();
		Map<String, String[]> parameters = new TreeMap<String, String[]>();
		parameters.put("page", new String[]{"42"});
		Query query = Query.create(parameters, coderMgr);
		
		assertTrue(query.hasPage());
		assertEquals(42L, query.getPage());
	}
	
	@Test
	public void testCreateQueryFromQueryStringWithReplies() {
		CoderManager coderMgr = new CoderManager();
		Map<String, String[]> parameters = new TreeMap<String, String[]>();
		parameters.put("replies", new String[]{"42"});
		Query query = Query.create(parameters, coderMgr);
		
		assertTrue(query.hasReplies());
		assertEquals(42, query.getReplies());
	}
	
	@Test
	public void testCreateQueryFromQueryStringWithQuery() {
		CoderManager coderMgr = new CoderManager();
		Map<String, String[]> parameters = new TreeMap<String, String[]>();
		parameters.put("query", new String[]{"42"});
		Query query = Query.create(parameters, coderMgr);
		
		assertTrue(query.hasSearchString());
		assertEquals("42", query.getSearchString());
	}
	
	@Test
	public void testCreateQueryFromQueryStringWithFeed() {
		CoderManager coderMgr = new CoderManager();
		Map<String, String[]> parameters = new TreeMap<String, String[]>();
		parameters.put("feed", new String[]{"foo"});
		Query query = Query.create(parameters, coderMgr);
		
		assertTrue(query.hasFeed());
		assertTrue(query.hasFeed("foo"));
		assertEquals(1, query.getFeeds().size());
	}
	
	@Test
	public void testCreateQueryFromQueryStringWithLanguage() {
		CoderManager coderMgr = new CoderManager();
		Map<String, String[]> parameters = new TreeMap<String, String[]>();
		parameters.put("lang", new String[]{"en"});
		Query query = Query.create(parameters, coderMgr);
		
		assertTrue(query.hasLanguage());
		assertEquals("en", query.getLanguage());
	}
	
	@Test
	public void testCreateQueryFromQueryStringWithSort() {
		CoderManager coderMgr = new CoderManager();
		Map<String, String[]> parameters = new TreeMap<String, String[]>();
		parameters.put("sort", new String[]{"afs:foo"});
		Query query = Query.create(parameters, coderMgr);
		
		assertTrue(query.hasSort());
		assertEquals("afs:foo", query.getSort());
	}
	
	@Test
	public void testCreateQueryFromQueryStringWithFilter() {
		CoderManager coderMgr = new CoderManager();
		Map<String, String[]> parameters = new TreeMap<String, String[]>();
		parameters.put("filter", new String[]{"foo_bar"});
		Query query = Query.create(parameters, coderMgr);
		
		assertTrue(query.hasFilter());
		String[] values = query.getFilterValues("foo");
		assertEquals(1, values.length);
		assertEquals("bar", values[0]);
	}
	
	@Test
	public void testCreateQueryFromQueryStringWithPageAndOtherParameter() {
		CoderManager coderMgr = new CoderManager();
		Map<String, String[]> parameters = new TreeMap<String, String[]>();
		parameters.put("replies", new String[]{"42"});
		parameters.put("page", new String[]{"666"});
		Query query = Query.create(parameters, coderMgr);
		
		assertTrue(query.hasPage());
		assertEquals(666, query.getPage());
	}
	
	@Test
	public void testCopyQuery() {
		String feed = "feed";
		String facetId = "facetId";
		String valueKey = "facetValue";
		String langAndRegionCodes = "en-US";
		long pageNo = 42L;
		String query = "query";
		int repliesNb = 666;
		String sortOrder = "afs:foo";
		Query reference = Query.create().addFeed(feed).addFilter(facetId, valueKey)
				.setLanguage(langAndRegionCodes).setSearchString(query)
				.setReplies(repliesNb).setSort(sortOrder).setPage(pageNo);
		Query copy = new Query(reference);
		
		assertEquals(1, copy.getFeeds().size());
		assertEquals(feed, copy.getFeeds().toArray()[0]);
		assertEquals(1, copy.getFilters().size());
		assertTrue(copy.getFilters().containsKey(facetId));
		assertEquals(1, copy.getFilterValues(facetId).length);
		assertEquals(valueKey, copy.getFilterValues(facetId)[0]);
		assertEquals(langAndRegionCodes, copy.getLanguage());
		assertEquals(pageNo, copy.getPage());
		assertEquals(query, copy.getSearchString());
		assertEquals(repliesNb, copy.getReplies());
		assertEquals(sortOrder, copy.getSort());
	}
	
	@Test
	public void testCopyQueryAndUpdateCopy() {
		String feed = "feed";
		String facetId = "facetId";
		String valueKey = "facetValue";
		String langAndRegionCodes = "en-US";
		long pageNo = 42;
		String query = "query";
		int repliesNb = 666;
		String sortOrder = "afs:foo";
		Query reference = Query.create().addFeed(feed).addFilter(facetId, valueKey)
				.setLanguage(langAndRegionCodes).setSearchString(query)
				.setReplies(repliesNb).setSort(sortOrder).setPage(pageNo);
		
		Query copy = new Query(reference);
		copy.setFeed("foo");
		copy.setFilter("bar", "baz");
		copy.setLanguage("es-ES");
		copy.setPage(88);
		copy.setSearchString("arg");
		copy.setReplies(99);
		copy.setSort("afs:bar");
		
		assertEquals(1, reference.getFeeds().size());
		assertEquals(feed, reference.getFeeds().toArray()[0]);
		assertEquals(1, reference.getFilters().size());
		assertTrue(reference.getFilters().containsKey(facetId));
		assertEquals(1, reference.getFilterValues(facetId).length);
		assertEquals(valueKey, reference.getFilterValues(facetId)[0]);
		assertEquals(langAndRegionCodes, reference.getLanguage());
		assertEquals(pageNo, reference.getPage());
		assertEquals(query, reference.getSearchString());
		assertEquals(repliesNb, reference.getReplies());
		assertEquals(sortOrder, reference.getSort());
	}
}
