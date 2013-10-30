package net.antidot.api.search;

import static org.junit.Assert.*;
import static org.mockito.Mockito.*;

import java.net.URISyntaxException;
import java.util.Set;
import java.util.TreeMap;
import java.util.TreeSet;

import org.junit.Before;
import org.junit.Test;

public class QueryCoderTest {

	private CoderManager coderMgr;
	private Query query;

	@SuppressWarnings("unchecked")
	@Before
	public void setUp() {
		query = mock(Query.class);
		when(query.hasFeed()).thenReturn(false);
		when(query.hasSearchString()).thenReturn(false);
		when(query.hasPage()).thenReturn(false);
		when(query.hasReplies()).thenReturn(false);
		when(query.hasLanguage()).thenReturn(false);
		when(query.hasSort()).thenReturn(false);
		when(query.hasFilter()).thenReturn(false);
		
		FeedCoderInterface feedCoder = mock(FeedCoderInterface.class);
		when(feedCoder.encode(anySetOf(String.class))).thenReturn("encodedFeed");
		
		FilterCoderInterface filterCoder = mock(FilterCoderInterface.class);
		when(filterCoder.encode(anyMap())).thenReturn("encodedFilter");
		
		coderMgr = new CoderManager(feedCoder, filterCoder);
	}
		
	@Test
	public void testLinkWithoutParameter() {
		try {
			QueryEncoder coder = new QueryEncoder("http://foo", coderMgr);
			assertEquals("http://foo", coder.generateLink(query));
		} catch (URISyntaxException e) {
			fail("Should not have failed with: " + e.getMessage());
		}
	}

	@Test
	public void testLinkWithFeed() {
		when(query.hasFeed()).thenReturn(true);
		when(query.getFeeds()).thenReturn(new TreeSet<String>());
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);

		try {
			assertEquals("http://foo?feed=encodedFeed", coder.generateLink(query));
		} catch (URISyntaxException e) {
			fail("Should not have failed with: " + e.getMessage());
		}
	}

	@Test
	public void testLinkWithPage() {
		when(query.hasPage()).thenReturn(true);
		when(query.getPage()).thenReturn(42L);
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);

		try {
			assertEquals("http://foo?page=42", coder.generateLink(query));
		} catch (URISyntaxException e) {
			fail("Should not have failed with: " + e.getMessage());
		}
	}

	@Test
	public void testLinkWithQuery() {
		when(query.hasSearchString()).thenReturn(true);
		when(query.getSearchString()).thenReturn("query");
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);

		try {
			assertEquals("http://foo?query=query", coder.generateLink(query));
		} catch (URISyntaxException e) {
			fail("Should not have failed with: " + e.getMessage());
		}
	}

	@Test
	public void testLinkWithReplies() {
		when(query.hasReplies()).thenReturn(true);
		when(query.getReplies()).thenReturn(666);
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);

		try {
			assertEquals("http://foo?replies=666", coder.generateLink(query));
		} catch (URISyntaxException e) {
			fail("Should not have failed with: " + e.getMessage());
		}
	}

	@Test
	public void testLinkWithLanguage() {
		when(query.hasLanguage()).thenReturn(true);
		when(query.getLanguage()).thenReturn("en-US");
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);

		try {
			assertEquals("http://foo?lang=en-US", coder.generateLink(query));
		} catch (URISyntaxException e) {
			fail("Should not have failed with: " + e.getMessage());
		}
	}

	@Test
	public void testLinkWithSort() {
		when(query.hasSort()).thenReturn(true);
		when(query.getSort()).thenReturn("sorted");
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);

		try {
			assertEquals("http://foo?sort=sorted", coder.generateLink(query));
		} catch (URISyntaxException e) {
			fail("Should not have failed with: " + e.getMessage());
		}
	}

	@Test
	public void testLinkWithFilter() {
		when(query.hasFilter()).thenReturn(true);
		when(query.getFilters()).thenReturn(new TreeMap<String, Set<String>>());
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);

		try {
			assertEquals("http://foo?filter=encodedFilter", coder.generateLink(query));
		} catch (URISyntaxException e) {
			fail("Should not have failed with: " + e.getMessage());
		}
	}
	
	@Test
	public void testLinkWithPageAndReplies() {
		when(query.hasPage()).thenReturn(true);
		when(query.getPage()).thenReturn(42L);
		QueryEncoder coder = null;
		coder = new QueryEncoder("http://foo", coderMgr);
		try {
			assertEquals("http://foo?page=42", coder.generateLink(query));
		} catch (URISyntaxException e) {
			fail("Should not have failed with: " + e.getMessage());
		}

		when(query.hasReplies()).thenReturn(true);
		when(query.getReplies()).thenReturn(666);

		try {
			assertEquals("http://foo?page=42&replies=666", coder.generateLink(query));
		} catch (URISyntaxException e) {
			fail("Should not have failed with: " + e.getMessage());
		}

	}

}
