package net.antidot.api.search;


/** Basic string formatter.
 * <p> 
 * Simple formatter which add &lt;strong> tags around highlighted words. 
 * <p>
 * For example, if specific word 'foo' is looked for through Antidot search engine,
 * this word will be highlighted in title and abstract of the results.
 * <p> 
 * It is highly recommended to implement your own formatter for better word highlighting.
 */
public class BasicTextFormatter implements TextFormatter {
	/* (non-Javadoc)
	 * @see net.antidot.api.search.TextFormatter#text(java.lang.String)
	 */
	@Override
	public String text(String input) {
		return input;
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.search.TextFormatter#match(java.lang.String)
	 */
	@Override
	public String match(String input) {
		return "<strong>" + input + "</strong>";
	}

	/* (non-Javadoc)
	 * @see net.antidot.api.search.TextFormatter#trunc()
	 */
	@Override
	public String trunc() {
		return "…";
	}
}
