package net.antidot.api.search;

import static org.junit.Assert.*;

import java.util.Arrays;
import java.util.TreeSet;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.TreeMap;

import org.junit.Test;

public class DefaultFilterCoderTest {

	@Test
	public void testEncodeOneFilterWithOneValue() {
		String filter = "foo";
		String value = "bar";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter, new TreeSet<String>(Arrays.asList(value)));
		FilterCoderInterface coder = new DefaultFilterCoder();
		String expected = "foo_bar";
		
		assertEquals(expected, coder.encode(input));
	}
	
	@Test
	public void testEncodeOneFilterWithMultipleValues() {
		String filter = "foo";
		String first = "bar";
		String second = "baz";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter, new TreeSet<String>(Arrays.asList(first, second)));
		FilterCoderInterface coder = new DefaultFilterCoder();
		String expected = "foo_bar_baz";

		assertEquals(expected, coder.encode(input));
	}

	@Test
	public void testEncodeMultipleFiltersWithOneValue() {
		String filter1 = "foo";
		String first = "bar";
		String filter2 = "fouz";
		String second = "baz";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter1, new TreeSet<String>(Arrays.asList(first)));
		input.put(filter2, new TreeSet<String>(Arrays.asList(second)));
		FilterCoderInterface coder = new DefaultFilterCoder();
		String expected = "foo_bar-fouz_baz";

		assertEquals(expected, coder.encode(input));
	}
	
	@Test
	public void testEncodeMultipleFiltersWithMultipleValues() {
		String filter1 = "foo";
		String first = "bar";
		String second = "baz";
		String filter2 = "fouz";
		String third = "bat";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter1, new TreeSet<String>(Arrays.asList(first, second)));
		input.put(filter2, new TreeSet<String>(Arrays.asList(third)));
		FilterCoderInterface coder = new DefaultFilterCoder();
		String expected = "foo_bar_baz-fouz_bat";

		assertEquals(expected, coder.encode(input));
	}
	
	@Test
	public void testEncodeOneFilterContainingValueSeparator() {
		String filter = "foo_foo";
		String first = "bar";
		String second = "baz";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter, new TreeSet<String>(Arrays.asList(first, second)));
		FilterCoderInterface coder = new DefaultFilterCoder();
		String expected = "foo|_foo_bar_baz";

		assertEquals(expected, coder.encode(input));
	}
	
	@Test
	public void testEncodeOneFilterWithValueContainingValueSeparator() {
		String filter = "foo";
		String first = "bar_bar";
		String second = "baz";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter, new TreeSet<String>(Arrays.asList(first, second)));
		FilterCoderInterface coder = new DefaultFilterCoder();
		String expected = "foo_bar|_bar_baz";

		assertEquals(expected, coder.encode(input));
	}
	
	@Test
	public void testEncodeOneFilterContainingFilterSeparator() {
		String filter = "foo-foo";
		String first = "bar";
		String second = "baz";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter, new TreeSet<String>(Arrays.asList(first, second)));
		FilterCoderInterface coder = new DefaultFilterCoder();
		String expected = "foo|-foo_bar_baz";

		assertEquals(expected, coder.encode(input));
	}
	
	@Test
	public void testEncodeOneFilterWithValueContainingFilterSeparator() {
		String filter = "foo";
		String first = "bar";
		String second = "baz-baz";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter, new TreeSet<String>(Arrays.asList(first, second)));
		FilterCoderInterface coder = new DefaultFilterCoder();
		String expected = "foo_bar_baz|-baz";

		assertEquals(expected, coder.encode(input));
	}
	
	@Test
	public void testEncodeOneFilterContainingEscapeCharacter() {
		String filter = "foo|foo";
		String first = "bar";
		String second = "baz";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter, new TreeSet<String>(Arrays.asList(first, second)));
		FilterCoderInterface coder = new DefaultFilterCoder();
		String expected = "foo||foo_bar_baz";

		assertEquals(expected, coder.encode(input));
	}
	
	@Test
	public void testEncodeOneFilterWithValueContainingEscapeCharacter() {
		String filter = "foo";
		String first = "bar|bar";
		String second = "baz|";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter, new TreeSet<String>(Arrays.asList(first, second)));
		FilterCoderInterface coder = new DefaultFilterCoder();
		String expected = "foo_bar||bar_baz||";

		assertEquals(expected, coder.encode(input));
		
	}
	
	@Test
	public void testEncodeOneFilterWithMultipleValuesOverloadingValueSeparator() {
		String filter = "foo";
		String first = "bar";
		String second = "baz";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter, new TreeSet<String>(Arrays.asList(first, second)));
		FilterCoderInterface coder = new DefaultFilterCoder('%');
		String expected = "foo%bar%baz";

		assertEquals(expected, coder.encode(input));
	}
	
	@Test
	public void testEncodeMultipleFilterOverloadingFilterSeparator() {
		String filter1 = "foo";
		String first = "bar";
		String second = "baz";
		String filter2 = "fouz";
		String third = "bat";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter1, new TreeSet<String>(Arrays.asList(first, second)));
		input.put(filter2, new TreeSet<String>(Arrays.asList(third)));
		FilterCoderInterface coder = new DefaultFilterCoder('_', '%');
		String expected = "foo_bar_baz%fouz_bat";

		assertEquals(expected, coder.encode(input));
	}
	
	@Test
	public void testEncodeFilterOverloadingEscapeCharacter() {
		String filter = "foo_";
		String first = "bar-";
		String second = "baz%";
		Map<String, Set<String>> input = new TreeMap<String, Set<String>>();
		input.put(filter, new TreeSet<String>(Arrays.asList(first, second)));
		FilterCoderInterface coder = new DefaultFilterCoder('_', '-', '%');
		String expected = "foo%__bar%-_baz%%";

		assertEquals(expected, coder.encode(input));
	}
	
	
	@Test
	public void testDecodeOneFilterWithOneValue() {
		String input = "foo_bar";
		FilterCoderInterface coder = new DefaultFilterCoder();
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo", Arrays.asList("bar"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeOneFilterWithMultipleValues() {
		String input = "foo_bar_baz";
		FilterCoderInterface coder = new DefaultFilterCoder();
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo", Arrays.asList("bar", "baz"));
		assertEquals(expected, result);
	}

	@Test
	public void testDecodeMultipleFiltersWithOneValue() {
		String input = "foo_bar-fouz_baz";
		FilterCoderInterface coder = new DefaultFilterCoder();
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo", Arrays.asList("bar"));
		expected.put("fouz", Arrays.asList("baz"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeMultipleFiltersWithMultipleValues() {
		String input = "foo_bar_baz-fouz_bat_bas";
		FilterCoderInterface coder = new DefaultFilterCoder();
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo", Arrays.asList("bar", "baz"));
		expected.put("fouz", Arrays.asList("bat", "bas"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeOneFilterContainingValueSeparator() {
		String input = "foo|_foo_bar";
		FilterCoderInterface coder = new DefaultFilterCoder();
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo_foo", Arrays.asList("bar"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeOneFilterWithValueContainingValueSeparator() {
		String input = "foo_bar|_bar";
		FilterCoderInterface coder = new DefaultFilterCoder();
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo", Arrays.asList("bar_bar"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeOneFilterContainingFilterSeparator() {
		String input = "foo|-foo_bar";
		FilterCoderInterface coder = new DefaultFilterCoder();
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo-foo", Arrays.asList("bar"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeOneFilterWithValueContainingFilterSeparator() {
		String input = "foo_bar|-bar";
		FilterCoderInterface coder = new DefaultFilterCoder();
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo", Arrays.asList("bar-bar"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeOneFilterContainingEscapeCharacter() {
		String input = "foo||foo_bar";
		FilterCoderInterface coder = new DefaultFilterCoder();
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo|foo", Arrays.asList("bar"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeOneFilterWithValueContainingEscapeCharacter() {
		String input = "foo_bar||bar";
		FilterCoderInterface coder = new DefaultFilterCoder();
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo", Arrays.asList("bar|bar"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeOneFilterWithMultipleValuesOverloadingValueSeparator() {
		String input = "foo%bar%baz";
		FilterCoderInterface coder = new DefaultFilterCoder('%');
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo", Arrays.asList("bar", "baz"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeMultipleFilterOverloadingFilterSeparator() {
		String input = "foo_bar%fouz_baz";
		FilterCoderInterface coder = new DefaultFilterCoder('_', '%');
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo", Arrays.asList("bar"));
		expected.put("fouz", Arrays.asList("baz"));
		assertEquals(expected, result);
	}
	
	@Test
	public void testDecodeFilterOverloadingEscapeCharacter() {
		String input = "foo%_foo_bar%%-fouz%-fouz_baz";
		FilterCoderInterface coder = new DefaultFilterCoder('_', '-', '%');
		Map<String, List<String>> result = coder.decode(input);
		
		Map<String, List<String>> expected = new TreeMap<String, List<String>>();
		expected.put("foo_foo", Arrays.asList("bar%"));
		expected.put("fouz-fouz", Arrays.asList("baz"));
		assertEquals(expected, result);
	}
	
}
