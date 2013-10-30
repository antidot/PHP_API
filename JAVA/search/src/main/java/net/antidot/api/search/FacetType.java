package net.antidot.api.search;

/** Type of the facet.
 * <p>
 * Available types are basic types plus INTERVAL which is a shorthand
 * for intervals of one of the basic types.
 */
public enum FacetType {
	/** Facet with integer values. */
	INTEGER,
	/** Facet with floating point values. */
	REAL,
	/** Facet with string values. */
	STRING,
	/** Facet with date values. */
	DATE,
	/** Facet with boolean values. */
	BOOL,
	/** Facet with intervals of one of the previous types. */
	INTERVAL
}
