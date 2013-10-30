package net.antidot.api.search;

import static org.junit.Assert.*;

import java.util.Arrays;
import java.util.HashSet;
import java.util.List;

import org.junit.Test;

public class DefaultFeedCoderTest {

	@Test
	public void testDecodeOneFeed() {
		String feed = "foo";
		DefaultFeedCoder coder = new DefaultFeedCoder();
		List<String> decoded = coder.decode(feed);

		assertFalse(decoded.isEmpty());
		assertEquals(1, decoded.size());
		assertEquals(feed, decoded.get(0));
	}

	@Test
	public void testDecodeOneFeedContainingEscapeCharacter() {
		String feed = "foo|bar";
		String feedEscaped = "foo||bar";
		DefaultFeedCoder coder = new DefaultFeedCoder();
		List<String> decoded = coder.decode(feedEscaped);

		assertFalse(decoded.isEmpty());
		assertEquals(1, decoded.size());
		assertEquals(feed, decoded.get(0));
	}

	@Test
	public void testDecodeMultipleFeeds() {
		String first = "foo";
		String second = "bar";
		String separator = "_";
		DefaultFeedCoder coder = new DefaultFeedCoder();
		List<String> decoded = coder.decode(first + separator + second);
		
		assertFalse(decoded.isEmpty());
		assertEquals(2, decoded.size());
		assertEquals(first, decoded.get(0));
		assertEquals(second, decoded.get(1));
	}
	
	@Test
	public void testDecodeMultipleFeedsWithSpecificSeparator() {
		String first = "foo";
		String second = "bar";
		Character separator = '%';
		DefaultFeedCoder coder = new DefaultFeedCoder(separator);
		List<String> decoded = coder.decode(first + separator + second);
		
		assertFalse(decoded.isEmpty());
		assertEquals(2, decoded.size());
		assertEquals(first, decoded.get(0));
		assertEquals(second, decoded.get(1));
	}
	
	@Test
	public void testDecodeMultipleFeedsWithEscapedSpecificSeparator() {
		Character separator = '-';
		Character escape = '|';
		String first = "foo-bar";
		String firstEscaped = "foo|-bar";
		String second = "baz";
		DefaultFeedCoder coder = new DefaultFeedCoder(separator, escape);
		List<String> decoded = coder.decode(firstEscaped + separator + second);
		
		assertFalse(decoded.isEmpty());
		assertEquals(2, decoded.size());
		assertEquals(first, decoded.get(0));
		assertEquals(second, decoded.get(1));
	}
	
	@Test
	public void testEncodeOneFeed() {
		String input = "foo";
		DefaultFeedCoder coder = new DefaultFeedCoder();
		
		assertEquals(input, coder.encode(new HashSet<String>(Arrays.asList(input))));
	}
	
	@Test
	public void testEncodeOneFeedContainingEscapeCharacter() {
		String input = "foo|bar";
		String expected = "foo||bar";
		DefaultFeedCoder coder = new DefaultFeedCoder();
		
		assertEquals(expected, coder.encode(new HashSet<String>(Arrays.asList(input))));
	}
	
	@Test
	public void testEncodeMultipleFeeds() {
		String first = "foo";
		String second = "bar";
		DefaultFeedCoder coder = new DefaultFeedCoder();
		String expected = first + coder.getSeparator() + second;
		
		assertEquals(expected, coder.encode(new HashSet<String>(Arrays.asList(first, second))));
	}
	
	@Test
	public void testEncodeMultipleFeedsWithSpecificSeparator() {
		String first = "foo";
		String second = "bar";
		Character separator = '_';
		DefaultFeedCoder coder = new DefaultFeedCoder(separator);
		String expected = first + separator + second;
		
		assertEquals(expected, coder.encode(new HashSet<String>(Arrays.asList(first, second))));
	}
		
	@Test
	public void testEncodeOneFeedContainingSpecificSeparator() {
		String input = "foo_foo";
		Character separator = '_';
		DefaultFeedCoder coder = new DefaultFeedCoder(separator);
		String expected = "foo" + coder.getEscape() + "_foo";
		
		assertEquals(expected, coder.encode(new HashSet<String>(Arrays.asList(input))));
	}
	
	@Test
	public void testEncodeMultipleFeedsWithSpecificSeparatorAndEscape() {
		Character separator = '_';
		Character escape = '%';
		String first = "foo_foo";
		String second = "bar%bar%";
		String third = "%baz_%baz";
		String expected = "foo%_foo_bar%%bar%%_%%baz%_%%baz";
		DefaultFeedCoder coder = new DefaultFeedCoder(separator, escape);
		
		assertEquals(expected, coder.encode(new HashSet<String>(Arrays.asList(first, second, third))));
	}
}
