package net.antidot.api.common;

import net.antidot.api.search.ReplyException;

/** Invalid/unmanaged reply is provided by Antidot web service.
 * <p>
 * This exception should never occur.
 * It is defined and used to detect incompatibility issues while Antidot web services evolves.
 */
public class BadReplyException extends ReplyException {
	private static final long serialVersionUID = -97196914682145676L;

	/** Constructs a new exception with the specified detail message.
	 * @param message [in] the detail message.
	 */
	public BadReplyException(String message) {
		super(message);
	}
}
