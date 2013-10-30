package net.antidot.api.search;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Set;
import java.util.TreeMap;
import java.util.regex.Pattern;

import org.apache.commons.lang3.StringUtils;

/** Default implementation for filter coder.
 * <p> 
 * This implementation is really simple and can be lightly customized through constructor parameters.
 * <p>
 * It should be necessary to adapt, modify or implement a new filter coder depending on your needs.
 */
public class DefaultFilterCoder extends CoderBase implements FilterCoderInterface {

	public static final Character DEFAULT_VALUE_SEPARATOR = '_';
	public static final Character DEFAULT_FILTER_SEPARATOR = '-';

	private String valueSeparator;
	private String filterSeparator;
	private String encodeRegex;
	
	/** Constructs new instance with default parameters.
	 * Default value separator character ({@value #DEFAULT_VALUE_SEPARATOR}),
	 * filter separator character ({@value #DEFAULT_FILTER_SEPARATOR}) and
	 * escape character ({@value CoderBase#DEFAULT_ESCAPE}) will be used.
	 */
	public DefaultFilterCoder() {
		this(DEFAULT_VALUE_SEPARATOR);
	}
	
	/** Constructs new instance with specific value separator character.
	 * Default filter separator character ({@value #DEFAULT_FILTER_SEPARATOR}) and
	 * escape character ({@value CoderBase#DEFAULT_ESCAPE}) will be used.
	 * @param valueSeparator [in] value separator character to use.
	 */
	public DefaultFilterCoder(Character valueSeparator) {
		this(valueSeparator, DEFAULT_FILTER_SEPARATOR);
	}

	/** Constructs new instance with specific value separator and filter separator characters.
	 * Default escape character ({@value CoderBase#DEFAULT_ESCAPE}) will be used.
	 * @param valueSeparator [in] value separator character to use.
	 * @param filterSeparator [in] filter separator character to use.
	 */
	public DefaultFilterCoder(Character valueSeparator, Character filterSeparator) {
		this(valueSeparator, filterSeparator, DEFAULT_ESCAPE );
	}

	/** Constructs new instance with specific value separator, filter separator and escape characters.
	 * Default value separator character ({@value #DEFAULT_VALUE_SEPARATOR}),
	 * filter separator character ({@value #DEFAULT_FILTER_SEPARATOR}) and
	 * escape character ({@value CoderBase#DEFAULT_ESCAPE}) will be used.
	 * @param valueSeparator [in] value separator character to use.
	 * @param filterSeparator [in] filter separator character to use.
	 * @param escape [in] escape separator character to use.
	 */
	public DefaultFilterCoder(Character valueSeparator, Character filterSeparator, Character escape) {
		super(escape);
		if (escape.equals(filterSeparator)
				|| filterSeparator.equals(valueSeparator)
				|| escape.equals(valueSeparator)) {
			throw new IllegalArgumentException("Separator characters and escape character should be different");
		}

		this.valueSeparator = valueSeparator.toString();
		this.filterSeparator = filterSeparator.toString();
		this.encodeRegex = "(" + escapeRegexEscaped + "|" + Pattern.quote(this.filterSeparator) + "|" + Pattern.quote(this.valueSeparator) + ")";
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.search.FilterCoderInterface#encode(java.util.Map)
	 */
	@Override
	public String encode(Map<String, Set<String>> parameters) {
		ArrayList<String> filters = new ArrayList<String>(parameters.size());

		for (Entry<String, Set<String>> entry : parameters.entrySet()) {
			List<String> withFilter = new ArrayList<String>(entry.getValue().size() + 1);
			withFilter.add(entry.getKey());
			withFilter.addAll(entry.getValue());
			filters.add(escapeAndJoin(withFilter, encodeRegex, valueSeparator));
		}
		return StringUtils.join(filters, filterSeparator);
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.search.FilterCoderInterface#decode(java.lang.String)
	 */
	@Override
	public Map<String, List<String>> decode(String parameters) {
		Map<String, List<String>> result = new TreeMap<String, List<String>>();
		List<String> filters = split(parameters, filterSeparator);
		for (String filter : filters) {
			List<String> values = split(filter, valueSeparator);
			if (values.size() > 1) {
				result.put(values.get(0), values.subList(1, values.size()));
			}
		}
		return result;
	}
}
