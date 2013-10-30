package net.antidot.api.search;

/** Management of new facet value.
 * <p> 
 * When adding new facet value to filter on, two different cases occurs:
 * <dl>
 *   <dt>REPLACE</dt>
 *   <dd>New facet value replace existing one.
 *   This means that you cannot filter one multiple facet values.</dd>
 *   <dt>ADD</dt>
 *   <dd>New facet value is added to existing one, allowing filter on multiple facet values.</dd>
 * </dl>
 */
public enum FacetMode {
	/** New facet value replace any existing one. */
	REPLACE,
	/** New facet values are added to existing ones. */
	ADD
}
