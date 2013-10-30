package net.antidot.api.search;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.Map;

/** Register for all configured facets.
 */
public class FacetRegistry {
	private Map<String, Facet> facets = new HashMap<String, Facet>();
	private Collection<Facet> orderedFacets = new ArrayList<Facet>();
	
	/** Appends new facet to the list of registered facets.
	 * <p><em>Notes:</em>
	 * <ul>
	 *   <li>Facets are identified by their id, so only one facet can be registered with a given identifier.</li>
	 *   <li>Order of added facets influence directly order of facets in result from Antidot search engine.</li>
	 * </ul>
	 * @param facet [in] new facet to be added.
	 * @exception IllegalArgumentException when a facet with same id has already been registered.
	 */
	public void addFacet(Facet facet) {
		String id = facet.getId();
		if (facets.containsKey(id)) {
			throw new IllegalArgumentException("Facet with id (" + id + ") already present in registry");
		}
		facets.put(id, facet);
		orderedFacets.add(facet);
	}
	
	/** Retrieves ordered list of configured facets.
	 * @return unmodifiable list of facets.
	 */
	public Collection<Facet> getFacets() {
		return Collections.unmodifiableCollection(orderedFacets);
	}
	
	/** Retrieves specific facet configuration.
	 * @param facetId [in] identifier of the facet to be retrieved.
	 * @return facet configuration.
	 * @throws IllegalArgumentException when requested facet identifier has not been registered yet.
	 */
	public Facet getFacet(String facetId) {
		Facet result = facets.get(facetId);
		if (result == null) {
			throw new IllegalArgumentException("No facet available with id: " + facetId);
		}
		return result;
	}
}
