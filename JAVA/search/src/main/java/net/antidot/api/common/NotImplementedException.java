package net.antidot.api.common;

/** Error raised on call to unimplemented method. 
 * <p> 
 * Such exception should never arise otherwise contact Antidot support team.
 */
public class NotImplementedException extends RuntimeException {
	private static final long serialVersionUID = 4449010408292595295L;

	/** Constructs a new exception with the specified detail message.
	 * @param message [in] the detail message.
	 */
	public NotImplementedException(String message) {
		super(message);
	}
}
