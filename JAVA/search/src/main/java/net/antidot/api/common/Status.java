/**
 * 
 */
package net.antidot.api.common;

/** Antidot search service status.
 * <p> 
 * Each Antidot search service is defined by a status.
 * For production services, default status is @c stable.
 */
public enum Status {
	/** Stable: production */
	STABLE("stable"),
	/** RC: release candidate, last tests before moving to stable */
	RC("rc"),
	/** Alpha: first development level */
	ALPHA("alpha"),
	/** Beta: second development level */
	BETA("beta"),
	/** Sandbox: test purpose only */
	SANDBOX("sandbox"),
	/** Archive: no more used in production, kept for reference */
	ARCHIVE("archive");

	private String value;

	private Status(String value) {
		this.value = value;
	}
	
	/** Retrieves string representation of the status.
	 * @return status as String.
	 */
	public String value() {
		return this.value;
	}
}
