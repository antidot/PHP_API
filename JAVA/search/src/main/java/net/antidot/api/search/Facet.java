package net.antidot.api.search;

/** Facet configuration.
 * <p> 
 * Facet is used to filter on the characteristics of the corpus.
 * Filter can be applied on one or multiple characteristics or-combined or and-combined.  
 */
public class Facet {

	private String id;
	private FacetType type;
	private FacetMode mode;
	private FacetCombination combination;
	private FacetStickyness stickyness;
	/** Used for string and boolean facets which need facet value enclosed between double quotes. */
	private String embracingCharacter;

	/** Constructs new facet configuration with its identifier and type.
	 * <p>
	 * Default mode is set to {@link FacetMode#REPLACE} which allows to filter on one facet value only.
	 * The facet is also defined as {@link FacetStickyness#STICKY} (see <a href="http://doc.afs-antidot.net/en/search#search;query=%2522What+is+a+sticky+facet%253F%2522">What is a sticky facet?</a>).
	 * @param id [in] facet identifier. This identifier should match facet declaration on indexation side.
	 * @param type [in] facet type (see {@link FacetType}). The type must match with the one defined on indexation side.
	 */
	public Facet(String id, FacetType type) {
		this(id, type, FacetMode.REPLACE);
	}
	
	/** Constructs new facet configuration with its identifier, type and mode.
	 * <p>
	 * Default facet value combination is set to {@link FacetCombination#OR}.
	 * This combination type has no effect when facet mode is set to {@link FacetMode#REPLACE}
	 * The facet is also defined as {@link FacetStickyness#STICKY} (see <a href="http://doc.afs-antidot.net/en/search#search;query=%2522What+is+a+sticky+facet%253F%2522">What is a sticky facet?</a>).
	 * @param id [in] facet identifier. This identifier should match facet declaration on indexation side.
	 * @param type [in] facet type (see {@link FacetType}). The type must match with the one defined on indexation side.
	 * @param mode [in] mode used when setting new value to filter on (see {@link FacetMode}).
	 */
	public Facet(String id, FacetType type, FacetMode mode) {
		this(id, type, mode, FacetCombination.OR);
	}

	/** Constructs new facet configuration with its identifier, type, mode and value combination.
	 * <p>
	 * The facet is defined as {@link FacetStickyness#STICKY} when facet value combination is set to {@link FacetCombination#OR},
	 * otherwise it is set to {@link FacetStickyness#NON_STICKY} (see <a href="http://doc.afs-antidot.net/en/search#search;query=%2522What+is+a+sticky+facet%253F%2522">What is a sticky facet?</a>).
	 * @param id [in] facet identifier. This identifier should match facet declaration on indexation side.
	 * @param type [in] facet type (see {@link FacetType}). The type must match with the one defined on indexation side.
	 * @param mode [in] mode used when setting new value to filter on (see {@link FacetMode}).
	 * @param combination [in] combination of values when filtering on multiple facet values (see {@link FacetCombination}).
	 */
	public Facet(String id, FacetType type, FacetMode mode,
			FacetCombination combination) {
		this(id, type, mode, combination, FacetStickyness.UNDEFINED);
	}

	/** Constructs new facet configuration with its identifier, type, mode, value combination and facet stickyness.
	 * @param id [in] facet identifier. This identifier should match facet declaration on indexation side.
	 * @param type [in] facet type (see {@link FacetType}). The type must match with the one defined on indexation side.
	 * @param mode [in] mode used when setting new value to filter on (see {@link FacetMode}).
	 * @param combination [in] combination of values when filtering on multiple facet values (see {@link FacetCombination}).
	 * @param stickyness [in] define whether the facet is {@link FacetStickyness#STICKY} or {@link FacetStickyness#NON_STICKY} (see <a href="http://doc.afs-antidot.net/en/search#search;query=%2522What+is+a+sticky+facet%253F%2522">What is a sticky facet?</a>).
	 */
	public Facet(String id, FacetType type, FacetMode mode,
			FacetCombination combination, FacetStickyness stickyness) {
		this.id = id;
		this.mode = mode;
		this.combination = combination;
		initializeStickyness(stickyness);
		initializeEmbracingCharacter(type);
	}

	private void initializeEmbracingCharacter(FacetType type) {
		this.type = type;
		if (type.equals(FacetType.STRING) || type.equals(FacetType.DATE)) {
			this.embracingCharacter = "\"";
		} else {
			this.embracingCharacter = "";
		}
	}

	private void initializeStickyness(FacetStickyness stickyness) {
		if (stickyness.equals(FacetStickyness.UNDEFINED)) {
			if (combination.equals(FacetCombination.OR)) {
				this.stickyness = FacetStickyness.STICKY;
			} else {
				this.stickyness = FacetStickyness.NON_STICKY;
			}
		} else {
			this.stickyness = stickyness;
		}
	}
	
	/** Retrieves facet identifier.
	 * @return id of the facet.
	 */
	public String getId() {
		return id;
	}
	
	/** Retrieves facet type.
	 * @return type of the facet.
	 */
	public FacetType getType() {
		return type;
	}
	
	/** Retrieves facet mode.
	 * @return mode of the facet.
	 */
	public FacetMode getMode() {
		return mode;
	}
	
	/** Checks whether facet mode is set to {@link FacetMode#REPLACE} mode.
	 * @return true when facet mode is {@link FacetMode#REPLACE}, false otherwise.
	 */
	public boolean isReplaceMode() {
		return mode == FacetMode.REPLACE;
	}
	
	/** Checks whether facet mode is set to {@link FacetMode#ADD} mode.
	 * @return true when facet mode is {@link FacetMode#ADD}, false otherwise.
	 */
	public boolean isAddMode() {
		return mode == FacetMode.ADD;
	}
	
	/** Retrieves facet values' combination.
	 * @return combination of values of the facet.
	 */
	public FacetCombination getCombination() {
		return combination;
	}
	
	/** Checks whether the facet is defined as {@link FacetStickyness#STICKY}.
	 * <p>
	 * Remember that a facet can have been defined as sticky facet on indexation side.
	 * The returned value reflect only the facet configuration on reply side only.
	 * @return true when the facet is sticky, false otherwise.
	 */
	public boolean isSticky() {
		return stickyness == FacetStickyness.STICKY;
	}
	
	/** Formats provided value so that it can be transmitted to Antidot search engine.
	 * <p>
	 * This method is defined to format appropriately filtering facet values according to facet type.
	 * @param value [in] input value to format.
	 * @return formatted value to be used along with Antidot search engine.
	 */
	public String formatValue(String value) {
		if (embracingCharacter.isEmpty()) {
			return value;
		} else {
			return embracingCharacter + value + embracingCharacter;
		}
	}
}
