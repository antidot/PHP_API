package net.antidot.api.search;

import java.util.ArrayList;
import java.util.List;

import net.antidot.protobuf.facets.FacetsProto.Interval;

/** Represents one value of interval facet.
 * <p>
 * You can get access to main data such as:
 * <ul>
 *   <li>key used to filter on specific facet value,</li>
 *   <li>label for better integration than simple key,</li>
 *   <li>count of documents defining the facet value,</li>
 *   <li>sub-facet values for hierarchical facets.</li>
 * </ul>
 */
public class IntervalFacetValueHelper implements FacetValueHelperInterface {

	private Interval interval;

	/** Constructs new value helper.
	 * You should never call this constructor directly.
	 * Instances of this class are constructed when necessary while querying {@link FacetHelper#getValues()}.
	 * @param interval [in] Google protobuf used to initialize this instance.
	 */
	public IntervalFacetValueHelper(Interval interval) {
		this.interval = interval;
	}
	
	/* (non-Javadoc)
	 * @see net.antidot.api.search.FacetValueHelperInterface#getKey()
	 */
	public String getKey() {
		return interval.getKey();
	}
	
	/* (non-Javadoc)
	 * @see net.antidot.api.search.FacetValueHelperInterface#getLabel()
	 */
	public String getLabel() {
		if (interval.getLabelsCount() > 0) {
			return interval.getLabels(0).getLabel();
		} else {
			return getKey();
		}
	}
	
	/* (non-Javadoc)
	 * @see net.antidot.api.search.FacetValueHelperInterface#getCount()
	 */
	public long getCount() {
		return interval.getItems();
	}
	
	/** List sub-values of this facet value.
	 * @return empty list of sub-values.
	 */
	public List<FacetValueHelperInterface> getValues() {
		return new ArrayList<FacetValueHelperInterface>();
	}
}
