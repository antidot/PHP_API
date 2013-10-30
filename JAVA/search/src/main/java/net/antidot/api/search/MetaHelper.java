package net.antidot.api.search;

import net.antidot.protobuf.agent.Agent.Type;
import net.antidot.protobuf.reply.ReplySetProto.Meta;

/** Helper to access main meta data from Antidot search engine reply.
 */
public class MetaHelper {
	private Meta meta;

	/** Constructs meta data helper from meta data protobuf.
	 * You should never need to create such object directly.
	 * This is managed by parent helper (see {@link ReplySetHelper}).
	 * @param metaPb [in] Google protobuf of the meta data of one reply set.
	 */
	public MetaHelper(Meta metaPb) {
		this.meta = metaPb;
	}

	/** Retrieves feed name of the reply set.
	 * @return feed name.
	 */
	public String getFeedName() {
		return meta.getUri();
	}

	/** Retrieves number of replies for this reply set.
	 * @return number of replies for the reply set.
	 */
	public long getReplyNb() {
		return meta.getTotalItems();
	}

	/** Retrieves duration to compute replies of this reply set.
	 * @return computation duration in milliseconds.
	 */
	public long getDuration() {
		return meta.getDurationMs();
	}	

	/** Retrieves producer of this reply set.
	 * @return reply set producer.
	 */
	public Type getProducer() {
		return meta.getProducer();
	}
}
