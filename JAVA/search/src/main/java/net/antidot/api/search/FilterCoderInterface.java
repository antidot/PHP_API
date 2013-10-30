package net.antidot.api.search;

import java.util.List;
import java.util.Map;
import java.util.Set;

/** Interface for coder of filters and filter values.
 * <p>
 * For a given Antidot search service, one or more filters can be queried with one or more values.
 * In order to be preserved between client and server, filter parameter can be encoded
 * in URL parameters. This interface defines methods which should be implemented for this purpose. 
 */
public interface FilterCoderInterface {
	/** Encodes filter parameters so that they can be given as URL parameters.
	 * @param map [in] parameters to encode.
	 * @return string encoded parameters.
	 */
	public String encode(Map<String, Set<String>> map);
	/** Decodes filter parameters from URL parameters.
	 * @param parameters [in] encoded parameters (for example result of {@link #encode(Map)}).
	 * @return list of decoded parameters.
	 */
	public Map<String, List<String>> decode(String parameters);
}
