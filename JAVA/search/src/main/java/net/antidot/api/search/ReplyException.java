package net.antidot.api.search;

/** Base class for internal runtime exception.
 */
public class ReplyException extends RuntimeException {

	private static final long serialVersionUID = -3077205232991964502L;

	/** Constructs a new exception with the specified detail message.
	 * @param message [in] the detail message.
	 */
	public ReplyException(String message) {
		super(message);
	}
}
