package net.antidot.api.search;

import java.net.URISyntaxException;

import net.antidot.api.common.ApiInternalError;

import org.apache.http.client.utils.URIBuilder;

/** Encodes queries to generate appropriate links.
 * <p>
 * Instance of this class should be used when generating result pages.
 */
public class QueryEncoder {

	private String uri;
	private CoderManager coderMgr;

	/** Constructs new query encoder.
	 * @param uri [in] URI base path.
	 * @param coderMgr [in] coder manager (feed coder, filter coder...).
	 * 		  It is used for link generation.
	 */
	public QueryEncoder(String uri, CoderManager coderMgr) {
		this.uri = uri;
		this.coderMgr = coderMgr;
	}
	
	/** Generates links for the given query.
	 * @param query [in] query to encode in order to generate corresponding link.
	 * @return URI corresponding to provided query. 
	 * @throws URISyntaxException cannot generate link.
	 * 		   It should only happen when the query encoder has been initialized with bad URI.
	 */
	public String generateLink(Query query) throws URISyntaxException {
		URIBuilder uriBuilder;
		try {
			uriBuilder = new URIBuilder(uri);
		} catch (URISyntaxException e) { // Should never happen
			throw new ApiInternalError("Cannot buil URI", e);
		}

		if (query.hasFeed()) {
			uriBuilder.addParameter(Query.FEED, coderMgr.getFeedCoder().encode(query.getFeeds()));
		}
		if (query.hasSearchString()) {
			uriBuilder.addParameter(Query.QUERY, query.getSearchString());
		}
		if (query.hasPage()) {
			uriBuilder.addParameter(Query.PAGE, String.valueOf(query.getPage()));
		}
		if (query.hasReplies()) {
			uriBuilder.addParameter(Query.REPLIES, String.valueOf(query.getReplies()));
		}
		if (query.hasLanguage()) {
			uriBuilder.addParameter(Query.LANG, query.getLanguage());
		}
		if (query.hasSort()) {
			uriBuilder.addParameter(Query.SORT, query.getSort());
		}
		if (query.hasFilter()) {
			uriBuilder.addParameter(Query.FILTER, coderMgr.getFilterCoder().encode(query.getFilters()));
		}

		return uriBuilder.build().toString();
	}
}
