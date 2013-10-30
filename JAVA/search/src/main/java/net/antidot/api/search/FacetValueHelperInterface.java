package net.antidot.api.search;

import java.util.List;

/** Interface representing one facet value.
 * <p>
 * You can get access to main data such as:
 * <ul>
 *   <li>key used to filter on specific facet value,</li>
 *   <li>label for better integration than simple key,</li>
 *   <li>count of documents defining the facet value,</li>
 *   <li>sub-facet values for hierarchical facets.</li>
 * </ul>
 */
public interface FacetValueHelperInterface {
	/** Retrieves key of the facet value.
	 * This key is used to filter on this specific facet value.
	 * @return key of the facet value.
	 */
	public String getKey();
	/** Retrieves label of the facet value.
	 * This label can be used instead of the <tt>key</tt> for a better web integration.
	 * <p> 
	 * If no label has been defined on PaF side,
	 * this method should return same result as {@link #getKey()} method.
	 * @return label of the facet value.
	 */
	public String getLabel();
	/** Retrieves number of documents which defines this specific facet value.
	 * @return number of documents defining this facet value.
	 */
	public long getCount();
	/** Lists sub-values of this facet value.
	 * This list can be empty if sub-values are not applicable.
	 * @return sub-values of the facet value.
	 */
	public List<FacetValueHelperInterface> getValues();
}
