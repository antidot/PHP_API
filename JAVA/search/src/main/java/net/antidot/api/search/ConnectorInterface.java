package net.antidot.api.search;

import java.io.IOException;
import java.util.Collection;
import java.util.Map;

/** Antidot search engine connector interface. 
 */
public interface ConnectorInterface {

	/** Sends query with specified parameters.
	 * @param params [in] parameters used to query Antidot search engine.
	 * @return byte array containing reply as Google protobuf.
	 * @throws IOException on various conditions.
	 */
	public abstract byte[] send(Map<String, Collection<String>> params)
			throws IOException;

}