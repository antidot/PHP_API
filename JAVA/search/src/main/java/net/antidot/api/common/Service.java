/**
 * 
 */
package net.antidot.api.common;

/** Antidot search service.
 * <p> 
 * Search service provided by Antidot.
 * Antidot search service is identified by a number and a status.
 * These values are provided by Antidot integration team.
 */
public class Service {
	private int id;
	private Status status;

	/** Constructs service instance with default status.
	 * Default status is @a STABLE (see {@link Status}).
	 * @param id [in] service identifier
	 */
	public Service(int id) {
		this(id, Status.STABLE);
	}

	/** Creates service instance.
	 * @param id [in] service identifier
	 * @param status [in] service status (see {@link Status})
	 */
	public Service(int id, Status status) {
		this.id = id;
		this.status = status;
	}
	
	/** Retrieves service id.
	 * @return service identifier
	 */
	public int getId() {
		return this.id;
	}
	
	/** Retrieves service status.
	 * @return string representation of the service status
	 */
	public String getStatus() {
		return this.status.value();
	}
}
