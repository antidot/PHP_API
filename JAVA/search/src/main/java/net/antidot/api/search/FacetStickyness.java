package net.antidot.api.search;

/** Sticky characteristic of facets. 
 * <p>
 * Facets can be:
 * <dl>
 *   <dt>STICKY</dt>
 *     <dd>Even if results are filtered on one facet value and results involve only this facet value,
 *      all facet values of the facet are still present in Antidot search engine replies.</dd>
 *   <dt>NON_STICKY</dt>
 *     <dd>Only facet values involved in Antidot search engine replies are present in the result.</dd>
 * </dl>
 * For more details, see <a href="http://doc.afs-antidot.net/en/search#search;query=%2522What+is+a+sticky+facet%253F%2522">What is a sticky facet?</a>.
 */
public enum FacetStickyness {
	/** Internal use to allow auto configuration depending on other facet parameters. */
	UNDEFINED,
	/** All facet values are present in output replies. */
	STICKY,
	/** Only relevant facet values are present in output replies. */
	NON_STICKY
}
