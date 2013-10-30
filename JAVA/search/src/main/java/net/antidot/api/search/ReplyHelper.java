package net.antidot.api.search;

import java.util.Iterator;

import net.antidot.api.common.BadReplyException;
import net.antidot.protobuf.reply.ReplySetProto;
import net.antidot.protobuf.reply.ReplySetProto.Kwic;
import net.antidot.protobuf.reply.ReplySetProto.Reply;

/** Helper for one reply.
 */
public class ReplyHelper {

	private Reply reply;

	/** Constructs reply helper from Google protobuf.
	 * You should never need to create such object directly.
	 * This is managed by parent helper (see {@link ReplySetHelper}).
	 * @param replyPb [in] one protobuf reply.
	 */
	public ReplyHelper(Reply replyPb) {
		this.reply = replyPb;
	}

	/** Retrieves document URI.
	 * @return URI of the document.
	 */
	public String getUri() {
		return reply.getUri().toStringUtf8();
	}

	/** Retrieves document title.
	 * Default formatter is used (see {@link BasicTextFormatter}), so matched words are simply highlighted.
	 * @return title of the document.
	 */
	public String getTitle() {
		return getTitle(new BasicTextFormatter());
	}

	/** Retrieves document title.
	 * @param formatter [in] specific formatter used to managed highlighted words.
	 * @return formatted title of the document.
	 */
	public String getTitle(TextFormatter formatter) {
		return getFormattedText(formatter, reply.getTitleList().iterator());
	}
	
	/** Retrieves document abstract.
	 * Default formatter is used (see {@link BasicTextFormatter}), so matched words are simply highlighted.
	 * @return abstract of the document
	 */
	public String getAbstract() {
		return getAbstract(new BasicTextFormatter());
	}
	
	/** Retrieves document abstract.
	 * @param formatter [in] specific formatter used to managed highlighted words.
	 * @return formatted abstract of the document.
	 */
	public String getAbstract(TextFormatter formatter) {
		return getFormattedText(formatter, reply.getAbstractList().iterator());
	}

	private String getFormattedText(TextFormatter formatter, Iterator<Kwic> it) {
		StringBuilder result = new StringBuilder();
		while (it.hasNext()) {
			Kwic kwic = it.next();
			String text = null;
			if (kwic.hasExtension(ReplySetProto.kwicStringExt)) {
				text = formatter.text(kwic.getExtension(ReplySetProto.kwicStringExt).getText());
			} else if (kwic.hasExtension(ReplySetProto.kwicMatchExt)) {
				text = formatter.match(kwic.getExtension(ReplySetProto.kwicMatchExt).getMatch());
			} else if (kwic.hasExtension(ReplySetProto.kwicTruncateExt)) {
				text = formatter.trunc();
			} else {
				throw new BadReplyException("Kwic is not of the supported type [" + kwic.toString() + "]");
			}	
			result.append(text);
		}
		return result.toString();
	}
}
