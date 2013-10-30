package net.antidot.api.search;

import java.net.URISyntaxException;
import java.util.List;

import net.antidot.api.common.ApiInternalError;
import net.antidot.protobuf.facets.FacetsProto.Facet;
import net.antidot.protobuf.lang.Label.LocalizedLabel;

/** Helper to access main facet data from Antidot search engine reply.
 */
public class FacetHelper {
	private Facet facetPb;
	private net.antidot.api.search.Facet facet;
	private QueryEncoder queryCoder;
	private Query query;

	/** Constructs facet helper from Google protobuf.
	 * <p>
	 * You should never need to create such object directly.
	 * This is managed by parent helper (see {@link ReplySetHelper}).
	 * @param facetPb [in] one protobuf facet.
	 * @param facetRegistry [in] registry of all configured facets. Appropriate facet is retrieved from the registry and later used to generate links.
	 * @param queryCoder [in] coder used to generate links.
	 * @param query [in] query corresponding to current result page. It is also used to generate links. 
	 * @throws IllegalArgumentException when facet identifier has not been registered to facet registry.
	 */
	public FacetHelper(Facet facetPb, FacetRegistry facetRegistry, QueryEncoder queryCoder, Query query) {
		this.facetPb = facetPb;
		this.facet = facetRegistry.getFacet(getId());
		this.queryCoder = queryCoder;
		this.query = query;
	}

	/** Retrieves facet identifier.
	 * @return identifier of the facet.
	 */
	public String getId() {
		return facetPb.getId();
	}

	/** Retrieves facet label.
	 * <p>
	 * Empty String is returned when no label has been defined for the facet.
	 * <p>
	 * When multiple labels are available, only the first one is retrieved.
	 * This can happen when labels are set for different localizations
	 * and no filter has been applied on any language through <tt>afs:lang</tt> filter. 
	 * @return label of the facet.
	 */
	public String getLabel() {
		List<LocalizedLabel> labels = facetPb.getLabelsList();
		if (labels.isEmpty()) {
			return "";
		} else {
			return labels.get(0).getLabel();
		}
	}

	/** Retrieves facet values.
	 * @return all values of the facets.
	 */
	public List<FacetValueHelperInterface> getValues() {
		return FacetValueBuilder.newBuilder(facetPb).build();
	}
	
	/** Retrieves link for result page filtered on specified facet value.
	 * <p>
	 * Exact filtering depends on current query and facet configuration.
	 * @param value [in] value of the facet to filter on (see {@link FacetValueHelperInterface#getKey()}
	 * @return appropriate link page.
	 * @throws URISyntaxException cannot generate link (should not happen).
	 */
	public String getLink(String value) throws URISyntaxException {
		Query theQuery = null;
		if (isSet(value)) {
			theQuery = query.removeFilter(getId(), value);
		} else {
			if (facet.isReplaceMode()) {
				theQuery = query.setFilter(getId(), value);
			} else if (facet.isAddMode()) {
				theQuery = query.addFilter(getId(), value);
			} else {
				throw new ApiInternalError("Unmanaged facet mode: " + facet.getMode());
			}
		}
		return queryCoder.generateLink(theQuery);
	}

	/** Checks whether specific facet value is set. 
	 * @param value [in] value to check.
	 * @return true when requested value is set for the facet, false otherwise.
	 */
	public boolean isSet(String value) {
		return query.hasFilter(getId(), value);
	}
}
