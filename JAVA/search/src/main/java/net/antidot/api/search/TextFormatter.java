package net.antidot.api.search;

/** Interface to format output text at your convenience.
 * <p>
 * Implement this interface to format title and abstract for AFS search engine reply.
 */
public interface TextFormatter {
	/** Formats non-matching text.
	 * <p>
	 * Input text is usually returned as is without any modification.
	 * @param input [in] one or more words considered as non-matching text.
	 * @return input formatted as required.
	 */
	public String text(String input);
	/** Formats text matching the query.
	 * <p>
	 * You probably want to embrace the <tt>input</tt> with <tt>&lt;b></tt> and <tt>&lt;/b></tt> tags
	 * or something more advanced depending on your integration.
	 * @param input [in] text matching the query.
	 * @return input formatted appropriately.
	 */
	public String match(String input);
	/** Format truncated text.
	 * <p>
	 * When title or abstract is too long, it is truncated.
	 * This method allows to specify which text will represent this truncation.
	 * <br/><tt>...</tt> characters are usually used in such case.
	 * @return truncated text representation.
	 */
	public String trunc();
}
