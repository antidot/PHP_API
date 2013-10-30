package net.antidot.api.upload;

/** Exception occurs when PaF does not exists.
 * <p>
 * Bad service id, service status or PaF name has been provided.
 */
public class FileUploadException extends RuntimeException {

	private static final long serialVersionUID = -9139491224822944568L;
	private long errorCode;
	private String details;

	/** Constructs new exception.
	 * @param errorCode [in] code of the error return by upload web service of Antidot Back Office.
	 * @param description [in] error description.
	 * @param details [in] details on the error.
	 */
	public FileUploadException(long errorCode, String description, String details) {
		super(description);
		this.errorCode = errorCode;
		this.details = details;
	}

	/** Retrieves error code.
	 * @return error code.
	 */
	public long getErrorCode() {
		return errorCode;
	}

	/** Retrieves additional details on the error.
	 * @return exception details.
	 */
	public String getDetails() {
		return details;
	}
}
