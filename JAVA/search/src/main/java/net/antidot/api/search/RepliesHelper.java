package net.antidot.api.search;

import java.util.Iterator;
import java.util.NoSuchElementException;

import org.apache.commons.lang3.StringUtils;

import net.antidot.protobuf.reply.Reply.replies;
import net.antidot.protobuf.reply.ReplySetProto.ReplySet;

/** Main helper to manager Antidot search engine replies.
 * <p>
 * This helper should be initialized with result of {@link QueryManager#send(Query)}.
 * <p>
 * <em>Reminder:</em>
 * <ul>
 *   <li>Result of Antidot search engine query is a <tt>replies</tt>.</li>
 *   <li><tt>replies</tt> is made of zero, one or more <tt>replySet</tt>.</li>
 *   <li>Which in turn is composed of zero, one or more <tt>facet</tt> and <tt>reply</tt>.</li>
 * </ul>
 * Hierarchy of helpers reflect this hierarchy.
 */
public class RepliesHelper {

	private replies replies;
	private Iterator<ReplySet> replySetIt = null;

	private FacetRegistry facetRegistry;
	private QueryEncoder queryCoder;
	private Query query;

	/** Constructs helper from Google protobuf.
	 * @param repliesPb [in] appropriately filled protobuf.
	 */
	public RepliesHelper(replies repliesPb, FacetRegistry facetRegistry, QueryEncoder queryCoder, Query query) {
		this.replies = repliesPb;
		this.facetRegistry = facetRegistry;
		this.queryCoder = queryCoder;
		this.query = query;
	}

	/** Checks whether error occurred.
	 * @return true on error, false otherwise.
	 */
	public boolean isError() {
		return replies.getHeader().hasError();
	}
	
	/** Retrieves error message.
	 * Empty string is returned when no error occurred. Please check errors first (see @a isError).
	 * @return error message.
	 */
	public String getErrorMessage() {
		if (isError()) {
			return StringUtils.join(replies.getHeader().getError().getMessageList(), "\n");
		} else {
			return "";
		}
	}

	/** Checks whether at least one reply set is available.
	 * @return true when one reply set is available, false otherwise.
	 */
	public boolean hasReplySet() {
		return replies.getReplySetCount() > 0;
	}

	/** Rewinds internal iterator on reply sets.
	 */
	public void rewind() {
		replySetIt = null;
	}
	
	/** Checks whether a reply set is available for next iteration.
	 * @return true when next call to {@link #next()} will return valid @link {@link RepliesHelper},
	 * false otherwise.
	 */
	public boolean hasNext() {
		if (! hasReplySet()) {
			return false;
		} else if (replySetIt == null) {
			initIterator();
		}
		return replySetIt.hasNext();
	}
	
	/** Retrieves next reply set.
	 * @return next available reply set.
	 */
	public ReplySetHelper next() {
		if (replySetIt == null) {
			initIterator();
		}
		return new ReplySetHelper(replySetIt.next(), facetRegistry, queryCoder, query);
	}
	
	/** Retrieves number of reply set.
	 * @return number of reply set in current reply.
	 */
	public int count() {
		return replies.getReplySetCount();
	}
	
	/** Retrieves first reply set.
	 * You should have previously checked that at least one reply set is available (see {@link #hasReplySet})
	 * @return first reply set.
	 */
	public ReplySetHelper getReplySet() {
		return getReply(0);
	}
	
	/** Retrieves desired reply set.
	 * @param replySetNo [in] reply set number to retrieve.
	 * @return reply set helper.
	 */
	public ReplySetHelper getReply(int replySetNo) {
		return new ReplySetHelper(replies.getReplySet(replySetNo), facetRegistry, queryCoder, query);
	}

	private void initIterator() {
		if (hasReplySet()) {
			replySetIt = replies.getReplySetList().iterator();
		} else {
			throw new NoSuchElementException("No reply set available");
		}
	}
}
