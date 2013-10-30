package net.antidot.api.search;

import java.util.List;
import java.util.Set;

/** Interface for coder of feed values.
 * <p>
 * For a given Antidot search service, one or more feeds can be queried.
 * In order to be preserved between client and server, feed parameter can be encoded
 * in URL parameters. This interface defines methods which should be implemented for this purpose. 
 */
public interface FeedCoderInterface {
	/** Encodes list of feed parameters so that they can be given as URL parameters.
	 * @param set [in] List of parameters to encode.
	 * @return string encoded parameters.
	 */
	public String encode(Set<String> set);
	/** Decodes feed parameters from URL parameters.
	 * @param parameters [in] encoded parameters (for example result of {@link #encode(Set)}).
	 * @return list of decoded parameters.
	 */
	public List<String> decode(String parameters);
}