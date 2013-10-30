package net.antidot.api.search;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Set;
import java.util.TreeMap;
import java.util.TreeSet;

import net.antidot.api.common.ApiInternalError;

import org.apache.commons.lang3.StringUtils;

/** Manages query.
 * <p>
 * Instances of this class should be used to query Antidot search engine.
 */
public class QueryManager {
	private ConnectorInterface connector;
	private FacetRegistry facetRegistry;

	/** Constructs new instance.
	 * @param connector [in] Antidot search engine connector.
	 * @param facetRegistry [in] register of all configured facets. 
	 */
	public QueryManager(ConnectorInterface connector, FacetRegistry facetRegistry) {
		this.connector = connector;
		this.facetRegistry = facetRegistry;
	}
	
	/** Sends new query to Antidot search engine.
	 * @param query [in] query to submit.
	 * @return reply in Google protobuf format.
	 * @throws IOException various error can occur when querying web services.
	 */
	public byte[] send(Query query) throws IOException {
		return connector.send(buildParameters(query));
	}

	private Map<String, Collection<String>> buildParameters(Query query) {
		Map<String, Collection<String>> result = convertToParameters(query);
		addFacetOption(result);
		return result;
	}

	private Map<String, Collection<String>> convertToParameters(Query query) {
		Map<String, Collection<String>> result = new TreeMap<String, Collection<String>>();
		if (query.hasFeed()) {
			result.put("afs:feed", query.getFeeds());
		}
		if (query.hasSearchString()) {
			result.put("afs:query", Arrays.asList(query.getSearchString()));
		}
		if (query.hasPage()) {
			result.put("afs:page", Arrays.asList(String.valueOf(query.getPage())));
		}
		if (query.hasReplies()) {
			result.put("afs:replies", Arrays.asList(String.valueOf(query.getReplies())));
		}
		if (query.hasLanguage()) {
			result.put("afs:lang", Arrays.asList(query.getLanguage()));
		}
		if (query.hasSort()) {
			result.put("afs:sort", Arrays.asList(query.getSort()));
		}
		if (query.hasFilter()) {
			addFilters(result, query);
		}
		return result;
	}

	private void addFilters(Map<String, Collection<String>> parameters, Query query) {
		Collection<String> values = new ArrayList<String>();
		for (Entry<String, Set<String>> filter : query.getFilters().entrySet()) {
			Facet facet = facetRegistry.getFacet(filter.getKey());
			String separator;
			if (facet.getCombination().equals(FacetCombination.AND)) {
				separator = " and ";
			} else if (facet.getCombination().equals(FacetCombination.OR)) {
				separator = " or ";
			} else {
				throw new ApiInternalError("Unmanaged combination mode: " + facet.getCombination().toString());
			}
			Set<String> escapedValues = new TreeSet<String>();
			for (String value : filter.getValue()) {
				escapedValues.add(filter.getKey() + "=" + facet.formatValue(value));
			}
			values.add(StringUtils.join(escapedValues, separator));
		}
		parameters.put("afs:filter", values);
	}

	private void addFacetOption(Map<String, Collection<String>> result) {
		Collection<Facet> facets = facetRegistry.getFacets();
		int facetNb = facets.size();
		if (facetNb > 0) {
			Collection<String> orderedFacets = new ArrayList<String>(facetNb);
			Collection<String> stickyFacets = new ArrayList<String>(facetNb);
			for (Facet facet : facets) {
				orderedFacets.add(facet.getId());
				if (facet.isSticky()) {
					stickyFacets.add(facet.getId() + ",sticky=true");
				}
			}
			result.put("afs:facetOrder", Arrays.asList(StringUtils.join(orderedFacets, ",")));
			if (! stickyFacets.isEmpty()) {
				result.put("afs:facet", stickyFacets);
			}
		}
	}
}
