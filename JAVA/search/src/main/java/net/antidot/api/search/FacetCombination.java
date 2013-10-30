package net.antidot.api.search;

/** Combination of the values of the facet.
 * <p> 
 * When multiple values are allowed they can be combined differently:
 * <dl>
 *   <dt>OR</dt>
 *   <dd>This is equivalent to join results of queries filtering on each facet value.
 *   <br/>For example, one can filter on <tt>red</tt> and <tt>blue</tt> ties.
 *   Red ties, blue ties and red+blue ties will be present in result set.</dd>
 *   <dt>AND</dt>
 *   <dd>This is equivalent to intersect results of queries filtering on each facet value.
 *   <br/>For example, one can filter on <tt>red</tt> and <tt>blue</tt> ties.
 *   Ties only red or blue will not be present in result set.</dd>
 * </dl>
 */
public enum FacetCombination {
	/** Join results of queries filtering on each facet value. */
	OR,
	/** Intersect results of queries filtering on each facet value. */
	AND
}
