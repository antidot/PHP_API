package net.antidot.api.search;

import java.util.List;
import java.util.Set;
import java.util.regex.Pattern;

/** Default implementation for feed coder.
 * <p> 
 * This implementation is really simple and can be lightly customized through constructor parameters.
 * <p>
 * It should be necessary to adapt, modify or implement a new feed coder depending on your needs.
 */
public class DefaultFeedCoder extends CoderBase implements FeedCoderInterface {

	public static final Character DEFAULT_SEPARATOR = '_';
	
	private String separator;
	private String separatorRegexEscaped;

	/** Constructs new instance with default parameters.
	 * Default separator character ({@value #DEFAULT_SEPARATOR})
	 * and default escape character ({@value #DEFAULT_ESCAPE}) will be used.
	 */
	public DefaultFeedCoder() {
		this(DEFAULT_SEPARATOR);
	}
	
	/** Constructs new instance with specific separator character.
	 * Default escape character ({@value #DEFAULT_ESCAPE}) will be used.
	 * @param separator [in] separator character.
	 */
	public DefaultFeedCoder(Character separator) {
		this(separator, DEFAULT_ESCAPE);
	}

	/** Constructs new instance with specific separator and escape characters.
	 * @param separator [in] separator character.
	 * @param escape [in] escape character.
	 */
	public DefaultFeedCoder(Character separator, Character escape) {
		super(escape);
		if (separator.equals(escape)) {
			throw new IllegalArgumentException("Separator and escape characters should be different");
		}

		this.separator = separator.toString();
		this.separatorRegexEscaped = Pattern.quote(this.separator);
	}
	
	/** Retrieves separator character.
	 * @return separator character as string.
	 */
	public String getSeparator() {
		return separator;
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.search.FeedCoderInterface#encode(java.util.Set)
	 */
	@Override
	public String encode(Set<String> parameters) {
		String regex = "(" + escapeRegexEscaped + "|" + separatorRegexEscaped + ")";
		return escapeAndJoin(parameters, regex, separator);
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.search.FeedCoderInterface#decode(java.lang.String)
	 */
	@Override
	public List<String> decode(String parameters) {
		return split(parameters, separator);
	}
}
