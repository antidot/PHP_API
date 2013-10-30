package net.antidot.api.common;

/** API internal exception.
 * <p> 
 * Such exception should never arise otherwise contact Antidot support team.
 */
public class ApiInternalError extends RuntimeException {
	private static final long serialVersionUID = 7748186275071011529L;

	/** Constructs a new exception with the specified detail message.
	 * @param message [in] the detail message.
	 */
	public ApiInternalError(String message) {
		super(message);
	}

	/** Constructs a new exception with the specified detail message and cause.
	 * @param message [in] the detail message.
	 * @param cause [in] the cause.
	 */
	public ApiInternalError(String message, Throwable cause) {
		super(message, cause);
	}
}
