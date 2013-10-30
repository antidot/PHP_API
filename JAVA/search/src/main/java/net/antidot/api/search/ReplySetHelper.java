package net.antidot.api.search;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import net.antidot.protobuf.facets.FacetsProto.Facet;
import net.antidot.protobuf.reply.ReplySetProto.Content;
import net.antidot.protobuf.reply.ReplySetProto.Reply;
import net.antidot.protobuf.reply.ReplySetProto.ReplySet;

/** Helper to access replies from one feed.
 */
public class ReplySetHelper {

	private ReplySet replySet;
	private MetaHelper meta;
	private PagerHelper pager;
	private ArrayList<ReplyHelper> replies;
	private ArrayList<FacetHelper> facets;

	private FacetRegistry facetRegistry;
	private QueryEncoder queryCoder;
	private Query query;

	/** Constructs helper from reply set protobuf.
	 * @param replySetPb [in] Google protobuf.
	 * @exception IncompleteReplySetException no meta data available for the reply set (this should never happen).
	 */
	public ReplySetHelper(ReplySet replySetPb, FacetRegistry facetRegistry, QueryEncoder queryCoder, Query query) {
		this.replySet = replySetPb;
		this.facetRegistry = facetRegistry;
		this.queryCoder = queryCoder;
		this.query = query;
		initializeHelpers();
	}

	/** Retrieves meta data of the reply set.
	 * @return reply set meta data.
	 */
	public MetaHelper getMeta() {
		return meta;
	}
	
	/** Checks whether reply set contains a pager.
	 * @return true when a pager is defined, false otherwise.
	 */
	public boolean hasPager() {
		return pager != null;
	}

	/** Retrieves pager of the reply set.
	 * Pager can be null when all results are present in the first page.
	 * @return pager of the reply set.
	 */
	public PagerHelper getPager() {
		return pager;
	}
	
	/** Retrieves replies of the reply set.
	 * Returned list can be empty if there is no reply available.
	 * @return replies of the reply set.
	 */
	public List<ReplyHelper> getReplies() {
		return replies;
	}
	
	/** Retrieves facets of the reply set.
	 * Returned list can be empty if there is no facet configured.
	 * @return facets of the reply set.
	 */
	public List<FacetHelper> getFacets() {
		return facets;
	}
	
	private void initializeHelpers() {
		initMeta();
		initPager();
		initReplies();
		initFacets();
	}
	
	private void initMeta() {
		if (replySet.hasMeta()) {
			meta = new MetaHelper(replySet.getMeta());
		} else {
			throw new IncompleteReplySetException("No meta data available");
		}
	}

	private void initPager() {
		if (replySet.hasPager()) {
			pager = new PagerHelper(replySet.getPager(), queryCoder, query);
		} else {
			pager = null;
		}
	}

	private void initReplies() {
		replies = new ArrayList<ReplyHelper>();
		if (replySet.hasContent()) {
			Content content = replySet.getContent();
			if (content.getReplyCount() == 0 && content.getClusterCount() != 0) {
				throw new UnsupportedOperationException("Clustered replies is not yet supported");
			}
			Iterator<Reply> replyIt = content.getReplyList().iterator();
			while (replyIt.hasNext()) {
				replies.add(new ReplyHelper(replyIt.next()));
			}
		}
	}

	private void initFacets() {
		facets = new ArrayList<FacetHelper>();
		if (replySet.hasFacets()) {
			Iterator<Facet> facetIt = replySet.getFacets().getFacetList().iterator();
			while (facetIt.hasNext()) {
				try {
					facets.add(new FacetHelper(facetIt.next(), facetRegistry, queryCoder, query));
				} catch (IllegalArgumentException e) {
					// No facet with that ID configured in the registry. Just skip it!
				}
			}
		}
	}
}
