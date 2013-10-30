package net.antidot.api.search;

import java.util.List;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Set;
import java.util.TreeMap;
import java.util.TreeSet;
import java.util.logging.Logger;
import java.util.regex.Pattern;

import net.antidot.common.lang.LangProtos.Lang;
import net.antidot.common.lang.RegionProtos.Region;
import net.antidot.protobuf.lang.Label.Language;

/** Immutable query object.
 * <p>
 * Manages all parameters necessary to query Antidot search engine
 * and generate new query links.
 * <p>
 * All set/add methods copy current query object,
 * then the copy is updated and finally is returned as the result of the methods. 
 */
public class Query {
	private Set<String> feeds = null;
	private String searchString = null;
	private Map<String, Set<String>> filters = null;
	private long page = 1;
	private int replies = REPLIES_NB;
	private Language lang = null;
	private String sort = null;
	
	public static final int REPLIES_NB = 10;
	public static String FEED = "feed";
	public static String QUERY = "query";
	public static String FILTER = "filter";
	public static String PAGE = "page";
	public static String REPLIES = "replies";
	public static String LANG = "lang";
	public static String SORT = "sort";

	/** Constructs new query by copying existing one.
	 * @param other [in] query to be copied.
	 */
	public Query(Query other) {
		feeds = new TreeSet<String>(other.feeds);
		searchString = other.searchString;
		filters = new TreeMap<String, Set<String>>();
		for (Entry<String, Set<String>> entry : other.filters.entrySet()) {
			filters.put(entry.getKey(), new TreeSet<String>(entry.getValue()));
		}
		page = other.page;
		replies = other.replies;
		lang = other.lang;
		sort = other.sort;
	}

	private Query() {
		feeds = new TreeSet<String>();
		filters = new TreeMap<String, Set<String>>();
	}
	
	/** Creates new query with default parameter values.
	 * @return newly created query.
	 */
	public static Query create() {
		return new Query();
	}
	
	/** Creates new query from parameters.
	 * <p>
	 * This method should be called with parameters coming from URL parameters.
	 * @param parameters [in] parameters used to initialize the query.
	 * @param coderMgr [in] manager for various coders (feed, filter...). 
	 * @return newly created query.
	 */
	public static Query create(Map<String, String[]> parameters, CoderManager coderMgr) {
		Query result = new Query();
		Integer page = null; // Page should be set last otherwise it is reset to 1
		for (Map.Entry<String, String[]> entry : parameters.entrySet()) {
			String key = entry.getKey();
			String[] values = entry.getValue(); // should always contain one value
			for (String value : values) {
				if (key.equals(FILTER)) {
					for (Entry<String, List<String>> decoded : coderMgr.getFilterCoder().decode(value).entrySet()) {
						for (String filterValue : decoded.getValue()) {
							result = result.addFilter(decoded.getKey(), filterValue);
						}
					}
				} else if (key.equals(QUERY)) {
					result = result.setSearchString(value);
				} else if (key.equals(PAGE)) {
					page = Integer.parseInt(value);
				} else if (key.equals(REPLIES)) {
					result = result.setReplies(Integer.parseInt(value));
				} else if (key.equals(FEED)) {
					for (String decoded : coderMgr.getFeedCoder().decode(value)) {
						result = result.addFeed(decoded);
					}
				} else if (key.equals(LANG)) {
					result = result.setLanguage(value);
				} else if (key.equals(SORT)) {
					result = result.setSort(value);
				} else {
					Logger.getLogger("search").warning("Ignoring unknown parameter: " + key);
				}
			}
		}
		if (page != null) {
			result = result.setPage(page);
		}
		return result;
	}
	
	private static Query copyAndResetPage(Query other) {
		Query result = new Query(other); 
		result.resetPage();
		return result;
	}

	/** Checks whether feed parameter is set.
	 * @return true when at least one feed has been set, false otherwise.
	 */
	public boolean hasFeed() {
		return ! feeds.isEmpty();
	}

	/** Checks whether specific feed parameter is set.
	 * @param feed [in] feed to check.
	 * @return true when at least one feed has been set, false otherwise.
	 */
	public boolean hasFeed(String feed) {
		return feeds.contains(feed);
	}
	
	/** Sets new feed value overwriting any existing one.
	 * @param feed [in] feed value to set.
	 * @return copy of the query with new parameter values.
	 */
	public Query setFeed(String feed) {
		Query result = copyAndResetPage(this);
		result.feeds.clear();
		result.feeds.add(feed);
		return result;
	}
	
	/** Adds new feed value to existing ones.
	 * @param feed [in] feed value to add.
	 * @return copy of the query with new parameter values.
	 */
	public Query addFeed(String feed) {
		Query result = copyAndResetPage(this);
		result.feeds.add(feed);
		return result;
	}
	
	/** Retrieves all defined feed values.
	 * @return list of feed values.
	 */
	public Set<String> getFeeds() {
		return feeds;
	}
	
	/** Checks whether a search string is defined.
	 * @return true when a search string is defined, false otherwise.
	 */
	public boolean hasSearchString() {
		return searchString != null;
	}
	
	/** Defines new search string.
	 * @param searchString [in] new search string value.
	 * @return copy of the query with new parameter values.
	 */
	public Query setSearchString(String searchString) {
		Query result = copyAndResetPage(this);
		result.searchString = searchString;
		return result;
	}
	
	/** Retrieves search string.
	 * @return search string.
	 */
	public String getSearchString() {
		return searchString;
	}
	
	/** Sets new filter value.
	 * <p>
	 * This replaces any existing value for the specified facet id.
	 * @param facetId [in] facet identifier for which new facet value should be associated.
	 * @param valueKey [in] facet value to set.
	 * @return copy of the query with new parameter values.
	 */
	public Query setFilter(String facetId, String valueKey) {
		Query result = copyAndResetPage(this);
		if (result.filters.containsKey(facetId)) {
			Set<String> values = result.filters.get(facetId);
			values.clear();
			values.add(valueKey);
		} else {
			result.createFilterEntry(facetId, valueKey);
		}
		return result;
	}
	
	/** Adds new filter value.
	 * <p>
	 * This adds new value to any existing one for the specified facet id.
	 * @param facetId [in] facet identifier for which new facet value should be associated.
	 * @param valueKey [in] facet value to add.
	 * @return copy of the query with new parameter values.
	 */
	public Query addFilter(String facetId, String valueKey) {
		Query result = copyAndResetPage(this);
		if (result.filters.containsKey(facetId)) {
			result.filters.get(facetId).add(valueKey);
		} else {
			result.createFilterEntry(facetId, valueKey);
		}
		return result;
	}

	private void createFilterEntry(String facetId, String valueKey) {
		Set<String> set = new TreeSet<String>();
		set.add(valueKey);
		filters.put(facetId, set);
	}
	
	/** Removes a filter values from the list of values associated to the specified facet identifier.
	 * @param facetId [in] facet identifier for which facet value should be removed.
	 * @param valueKey [in] facet value to remove.
	 * @return copy of the query with new parameter values.
	 */
	public Query removeFilter(String facetId, String valueKey) {
		Query result = copyAndResetPage(this);
		if (hasFilter(facetId, valueKey)) {
			Set<String> values = result.filters.get(facetId);
			values.remove(valueKey);
			if (values.isEmpty()) {
				result.filters.remove(facetId);
			}
		}
		return result;
	}
	
	/** Checks whether facet value is set for the specified facet identifier.
	 * @param facetId [in] facet identifier for which facet value should be checked.
	 * @param valueKey [in] facet value to check.
	 * @return true when facet value has been set for the specified facet identifier, false otherwise.
	 */
	public boolean hasFilter(String facetId, String valueKey) {
		if (filters.containsKey(facetId)) {
			return filters.get(facetId).contains(valueKey);
		}
		return false;
	}
	
	/** Retrieves all values set for the given facet identifier.
	 * @param facetId [in] facet identifier for which to retrieves all defined facet values.
	 * @return list of facet values.
	 * @exception IllegalArgumentException provided facet identifier is unknown. 
	 */
	public String[] getFilterValues(String facetId) {
		if (filters.containsKey(facetId)) {
			Set<String> values = filters.get(facetId);
			return values.toArray(new String[values.size()]);
		} else {
			throw new IllegalArgumentException("No facet defined with id: " + facetId);
		}
	}
	
	/** Retrieves all facets with their values.
	 * @return map of facet identifier / list of facet values.
	 */
	public Map<String, Set<String>> getFilters() {
		return filters;
	}

	/** Checks whether at least one facet has been defined. 
	 * @return true when one facet has been defined, false otherwise.
	 */
	public boolean hasFilter() {
		return ! filters.isEmpty();
	}	

	/** Checks whether specific page has been defined.
	 * <p>
	 * Default page number is <tt>1</tt> which is the default result page for Antidot search engine.
	 * @return true when specific page has been defined, false otherwise.
	 */
	public boolean hasPage() {
		return page != 1;
	}
	
	/** Defines new page number.
	 * <p>
	 * Page number should be greater than or equal to <tt>1</tt>.
	 * @param pageNo [in] new page number. 
	 * @return copy of the query with new parameter value.
	 * @exception IllegalArgumentException when page number does not match the constraint.
	 */
	public Query setPage(long pageNo) {
		if (pageNo < 1) {
			throw new IllegalArgumentException("Page number should be at least equal to 1");
		}
		Query result = new Query(this);
		result.page = pageNo;
		return result;
	}
	
	/** Defines new page number.
	 * @param pageNo [in] new page number. 
	 * @return copy of the query with new parameter value.
	 */
	public Query setPage(String pageNo) {
		return setPage(Integer.parseInt(pageNo));
	}
	
	protected void resetPage() {
		page = 1;
	}
	
	/** Retrieves page number.
	 * @return page number.
	 */
	public long getPage() {
		return page;
	}

	/** Checks whether number of replies is defined.
	 * <p>
	 * Number of replies is set to <tt>{@value #REPLIES_NB}</tt> by default.
	 * @return always true;
	 */
	public boolean hasReplies() {
		return true;
	}
	
	/** Sets new number of replies per page.
	 * @param repliesNb [in] new value for number of replies.
	 * @return copy of the query with new parameter value.
	 */
	public Query setReplies(int repliesNb) {
		Query result = copyAndResetPage(this);
		result.replies = repliesNb;
		return result;
	}
	
	/** Sets new number of replies per page.
	 * @param repliesNb [in] new value for number of replies.
	 * @return copy of the query with new parameter value.
	 */
	public Query setReplies(String repliesNb) {
		return setReplies(Integer.parseInt(repliesNb));
	}
	
	/** Retrieves number of replies per page.
	 * @return number of replies.
	 */
	public int getReplies() {
		return replies;
	}
	
	/** Checks whether language has been set. 
	 * @return true when specific value has been set for the language, false otherwise.
	 */
	public boolean hasLanguage() {
		return lang  != null;
	}
	
	/** Defines new language value.
	 * @param lang [in] language value to set.
	 * @return copy of the query with new parameter value.
	 */
	public Query setLanguage(Language lang) {
		Query result = copyAndResetPage(this);
		result.lang = lang;
		return result;
	}

	/** Defines new language value with language code only.
	 * @param langCode [in] language code to set.
	 * @return copy of the query with new parameter value.
	 */
	public Query setLanguage(Lang langCode) {
		return setLanguage(langCode, Region.UNKNOWN);
	}
	
	/** Defines new language value with language and country code.
	 * @param langCode [in] language code to set.
	 * @param regionCode [in] country code to set.
	 * @return copy of the query with new parameter value.
	 */
	public Query setLanguage(Lang langCode, Region regionCode) {
		return setLanguage(Language.newBuilder()
				.setLang(langCode)
				.setRegion(regionCode)
				.build());
	}

	/** Defines new language value with language code and optionally country code.
	 * @param langAndRegionCodes [in] language to set as string.
	 * 		  Allowed values are (case insensitive):
	 * <ul>
	 *   <li>two letters language code (ISO 639-1)</li>
	 *   <li>two letters language code (ISO 639-1) followed by
	 *   two letters country code (ISO 3166) separated by '_' (underscore),
	 *   '-' (hyphen) or nothing</li>
	 * </ul> 
	 * @return copy of the query with new parameter value.
	 */
	public Query setLanguage(String langAndRegionCodes) {
		int length = langAndRegionCodes.length();
		int startRegionCode = length;
		int endLangCode = langAndRegionCodes.indexOf("-");
		endLangCode = endLangCode == -1 ? langAndRegionCodes.indexOf("_") : endLangCode;
		if (endLangCode == -1) {
			if (length == 4) {
				endLangCode = 2;
				startRegionCode = 2;
			} else {
				endLangCode = length;
			}
		} else {
			startRegionCode = endLangCode + 1;
		}
		
		Lang lang = Lang.valueOf(langAndRegionCodes.substring(0, endLangCode).toUpperCase());
		Region region;
		
		if (startRegionCode >= length) {
			region = Region.UNKNOWN;
		} else {
			region = Region.valueOf(langAndRegionCodes.substring(startRegionCode, length).toUpperCase());
		}
		return setLanguage(lang, region);
	}
	
	/** Retrieves language as string value.
	 * <p>
	 * String format is two letters language code in lower case
	 * followed by hyphen followed by two letters country code in upper case.
	 * When no country has been defined, language code is only returned.
	 * <p>
	 * <em>Examples:</em> <tt>en-US</tt>, <tt>es</tt>.
	 * @return language.
	 */
	public String getLanguage() {
		if (lang == null) {
			return "";
		} else {
			String result = lang.getLang().toString().toLowerCase();
			if (lang.hasRegion() && lang.getRegion() != Region.UNKNOWN) {
				result += "-" + lang.getRegion().toString().toUpperCase();
			}
			return result;
		}
	}
	
	/** Retrieves language as Google protobuf.
	 * @return language protobuf.
	 */
	public Language getRawLanguage() {
		return lang;
	}
	
	/** Checks whether sort parameter has been set.
	 * @return true when sort parameter has been set, false otherwise.
	 */
	public boolean hasSort() {
		return sort  != null;
	}
	
	/** Defines new sort order replacing existing one.
	 * <p>
	 * Sort order should match following regular expression: <tt>afs:[a-zA-Z]+(?:,(?:ASC|DESC))?(?:;afs:[a-zA-Z]+(?:,(?:ASC|DESC))?)*</tt>.
	 * @param sortOrder [in] new sort order.
	 * @return copy of the query with new parameter value.
	 */
	public Query setSort(String sortOrder) {
		Query result = copyAndResetPage(this);
		if (sortOrder != null) {
			if (sortOrder.isEmpty()) {
				sortOrder = null;
			} else {
				String basicPattern = "afs:[a-zA-Z]+(?:,(?:ASC|DESC))?";
				Pattern pattern = Pattern.compile("^" + basicPattern + "(?:;" + basicPattern + ")*$");
				if (! pattern.matcher(sortOrder).matches()) {
					throw new IllegalArgumentException("Invalid sort order value provided: " + sortOrder);
				}
			}
		}
		result.sort = sortOrder;
		return result;
	}
	
	/** Reset sort parameter.
	 * @return copy of the query with new parameter value.
	 */
	public Query resetSort() {
		return setSort(null);
	}
	
	/** Retrieves sort parameter.
	 * @return sort parameter.
	 */
	public String getSort() {
		return sort;
	}

}
