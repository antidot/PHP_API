package net.antidot.api.search;

import java.net.URISyntaxException;
import java.util.List;
import java.util.NoSuchElementException;

import net.antidot.protobuf.query.PagerProto.Pager;

/** Helper to access pager information for a given reply set.
 */
public class PagerHelper {
	private Pager pager;
	private QueryEncoder queryCoder;
	private Query query;

	/** Constructs reply set pager helper.
	 * You should never need to create such object directly.
	 * This is managed by parent helper (see {@link ReplySetHelper}).
	 * @param pagerPb [in] Google protobuf.
	 * @param queryCoder [in] coder used to generate links.
	 * @param query [in] query corresponding to current result page. It is also used to generate links. 
	 */
	public PagerHelper(Pager pagerPb, QueryEncoder queryCoder, Query query) {
		this.pager = pagerPb;
		this.queryCoder = queryCoder;
		this.query = query;
	}
	
	/** Checks whether previous page exists.
	 * @return true when previous page exists, false otherwise.
	 */
	public boolean hasPrevious() {
		return pager.hasPreviousPage();
	}
	
	/** Retrieves previous page number.
	 * @return previous page number.
	 * @exception NoSuchElementException when previous page is not defined.
	 */
	public long getPrevious() {
		if (hasPrevious()) {
			return pager.getPreviousPage();
		} else {
			throw new NoSuchElementException("No previous page available");
		}
	}

	/** Checks whether next page exists.
	 * @return true when next page exists, false otherwise.
	 */
	public boolean hasNext() {
		return pager.hasNextPage();
	}
	
	/** Retrieves next page number.
	 * @return next page number.
	 * @exception NoSuchElementException when next page is not defined.
	 */
	public long getNext() {
		if (hasNext()) {
			return pager.getNextPage();
		} else {
			throw new NoSuchElementException("No next page available");
		}
	}

	/** Retrieves all pages as a list.
	 * @return list of pages.
	 */
	public List<Long> getPages() {
		return pager.getPageList();
	}

	/** Retrieves current page number. 
	 * @return current page number.
	 */
	public long getCurrent() {
		return pager.getCurrentPage();
	}
	
	/** Retrieves link to appropriate result page.
	 * @param pageNo [in] page to switch on.
	 * @return valid link for query with specified page number.
	 * @throws URISyntaxException cannot generate link (should not happen).
	 */
	public String getLink(Long pageNo) throws URISyntaxException {
		return queryCoder.generateLink(query.setPage(pageNo));
	}
}
