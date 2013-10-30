package net.antidot.api.search;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.LinkedList;
import java.util.List;
import java.util.regex.Pattern;

import org.apache.commons.lang3.StringUtils;

/** Base class for coder.
 * <p> 
 * Coders use generic methods to decode and/or encode data.
 * These methods are grouped here.
 */
public abstract class CoderBase {
	public static final Character DEFAULT_ESCAPE = '|';

	final protected String escape;
	final protected String escapeRegexEscaped;
	private String replacement;

	/** Standard constructor specifying escape character.
	 * @param escape [in] escape character used when encoding/decoding data.
	 */
	public CoderBase(Character escape) {
		this.escape = escape.toString();
		this.escapeRegexEscaped = Pattern.quote(this.escape);
		this.replacement = escape + "$1";
	}
	
	/** Retrieves escape character.
	 * @return escape character.
	 */
	public String getEscape() {
		return escape;
	}
	
	/** Escapes each element of the collection according to provided regex and joins them to build result.
	 * <tt>regex</tt> should contain a group which will be escaped by the escape character.
	 * <p>
	 * <strong>Example</strong>
	 * <ul>
	 *   <li>Let's suppose values contains following elements: <tt>foo_bar_baz</tt> and <tt>bat_bal</tt>.</li>
	 *   <li>With a regular expression defined as: <tt>(_|f)</tt>.</li>
	 *   <li>Result of the call to this method is: <tt>&lt;escape>foo&lt;escape>_bar&lt;escape>_baz&lt;joinCharacter>bat&lt;escape>_bal</tt>.</li>
	 * </ul>
	 * 
	 * @param values [in] list of value to work on.
	 * @param regex [in] regular expression to apply on each value of @a values.
	 * @param joinCharacter [in] character used to @a values after modification.
	 * @return string result of joined modified values.
	 */
	protected String escapeAndJoin(Collection<String> values, String regex, String joinCharacter) {
		ArrayList<String> escaped = new ArrayList<String>(values.size());
		for (String value : values) {
			escaped.add(value.replaceAll(regex, replacement));
		}
		return StringUtils.join(escaped, joinCharacter);
	}
	
	/** Splits input value according to split character.
	 * This is the inverse process of {@link #escapeAndJoin(Collection, String, String)}.
	 * <p>
	 * Each splitted element is unescaped.
	 * <p>
	 * <strong>Example</strong>
	 * <ul>
	 *   <li>Let's suppose value is: <tt>&lt;escape>foo&lt;escape>_bar&lt;escape>_baz&lt;joinCharacter>bat&lt;escape>_bal</tt>.</li>
	 *   <li>Result of the call to this method is list of following values: <tt>foo_bar_baz</tt> and <tt>bat_bal</tt>.</li>
	 * </ul>
	 * 
	 * @param value [in] value to split and unescape.
	 * @param splitCharacter [in] character used to split input.
	 * @return list of splitted elements.
	 */
	protected List<String> split(String value, String splitCharacter) {
		String splitCharacterRegexEscaped = Pattern.quote(splitCharacter);
		String splitRegex = "(?<!" + escapeRegexEscaped + ")" + splitCharacterRegexEscaped;
		LinkedList<String> result = new LinkedList<String>();
		// Split on escaped escape character
		String[] escaped = value.split("(?<=" + escapeRegexEscaped + ")" + escapeRegexEscaped);
		for (String esc : escaped) {
			String[] parts = esc.split(splitRegex);
			if (! result.isEmpty()) {
				parts[0] = result.removeLast() + parts[0];
			}
			result.addAll(Arrays.asList(parts));
		}
		return unescape(result, splitCharacter, splitCharacterRegexEscaped);
	}

	private List<String> unescape(LinkedList<String> values,
			String splitCharacter, String splitCharacterRegexEscaped) {
		List<String> result = new ArrayList<String>();
		String regex = escapeRegexEscaped + splitCharacterRegexEscaped;
		for (String value : values) {
			result.add(value.replaceAll(regex, splitCharacter));
		}
		return result;
	}
}
