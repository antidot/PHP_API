package net.antidot.api.search;

/** Invalid reply is provided by Antidot search engine. 
 * <p>
 * This exception should never occur.
 * It is defined and used to detect incompatibility issues while Antidot search engine evolves.
 */
public class IncompleteReplySetException extends ReplyException {

	private static final long serialVersionUID = -1244015220400033498L;

	/** Constructs a new exception with the specified detail message.
	 * @param message [in] the detail message.
	 */
	public IncompleteReplySetException(String message) {
		super(message);
	}
}
