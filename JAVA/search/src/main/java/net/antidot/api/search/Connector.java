/**
 * 
 */
package net.antidot.api.search;

import java.io.IOException;
import java.net.URI;
import java.net.URISyntaxException;
import java.util.Collection;
import java.util.Arrays;
import java.util.Map;
import java.util.Map.Entry;
import java.util.TreeMap;
import java.util.logging.Logger;

import net.antidot.api.common.ApiInternalError;
import net.antidot.api.common.BadReplyException;
import net.antidot.api.common.Scheme;
import net.antidot.api.common.Service;

import org.apache.http.HttpEntity;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.utils.URIBuilder;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.util.EntityUtils;
import org.apache.commons.validator.routines.DomainValidator;


/** Antidot search engine connector.
 * <p> 
 * This connector is used to interact with Antidot search engine.
 * <p>
 * Main method to call is {@link #send(Map)}.
 */
public class Connector implements ConnectorInterface {
	private Scheme scheme;
	private String host;
	private Service service;
	
	/** Constructs connector.
	 * Connector is created with default URL scheme AFS_SCHEME_HTTP (see {@link Scheme}).
	 * @param host [in] host name of the server hosting Antidot search service. 
	 * @param service [in] service to consider on the hosting server (see {@link Service})
	 */
	public Connector(String host, Service service) {
		this(Scheme.AFS_SCHEME_HTTP, host, service);
	}

	/** Constructs connector with specific URL scheme.
	 * This constructor allows to specify alternative scheme.
	 * @param scheme [in] URL scheme (currently only AFS_SCHEME_HTTP is supported)
	 * @param host [in] host name of the server hosting Antidot search service 
	 * @param service [in] service to consider on the hosting server (see {@link Service})
	 * @exception IllegalArgumentException when scheme argument is unsupported.
	 */
	public Connector(Scheme scheme, String host, Service service) {
		if (scheme != Scheme.AFS_SCHEME_HTTP) {
			throw new IllegalArgumentException("scheme argument should be set to AFS_SCHEME_HTTP");
		}
		if (! DomainValidator.getInstance().isValidLocalTld(host)) {
			Logger logger = Logger.getLogger("SearchConnector");
			logger.warning("Provided host name may be invalid: " + host);
		}
				
		this.scheme = scheme;
		this.host = host;
		this.service = service;
	}
	
	/* (non-Javadoc)
	 * @see net.antidot.api.search.ConnectorInterface#send(java.util.Map)
	 */
	@Override
	public byte[] send(Map<String, Collection<String>> params) throws IOException {
		Map<String, Collection<String>> parameters = defineDefaultParameters();
		parameters.putAll(params);
		URI uri = this.buildUri(parameters);
		CloseableHttpClient httpClient = HttpClients.createDefault();
		HttpGet httpGet = new HttpGet(uri);
		
		CloseableHttpResponse response = null;
		response = httpClient.execute(httpGet);
		try {
		    HttpEntity entity = response.getEntity();
		    if (entity != null) {
		    	try {
					return EntityUtils.toByteArray(entity);
				} catch (IOException e) {
					throw new BadReplyException("Cannot retrieve response content for the query");
				}
		    } else {
		    	throw new BadReplyException("Response has no content");
		    }
		} finally {
		    response.close();
		}
	}
	
	private Map<String, Collection<String>> defineDefaultParameters() {
		Map<String, Collection<String>> result = new TreeMap<String, Collection<String>>();
		result.put("afs:service", Arrays.asList(Integer.toString(this.service.getId())));
		result.put("afs:status", Arrays.asList(this.service.getStatus()));
		result.put("afs:output", Arrays.asList("protobuf")); // Or something else...
		return result;
	}

	private URI buildUri(Map<String, Collection<String>> params) {
		URIBuilder uriBuilder = getUriBuilder();
		for (Entry<String, Collection<String>> entry : params.entrySet()) {
			for (String value : entry.getValue()) {
				uriBuilder.setParameter(entry.getKey(), value);
			}
		}
		try {
			return uriBuilder.build();
		} catch (URISyntaxException e) {
			throw new ApiInternalError("Cannot build URI", e);
		}
	}
	
	public URIBuilder getUriBuilder() {
		return new URIBuilder().setScheme(this.scheme.value())
				.setHost(this.host)
				.setPath("/search");		
	}
}
