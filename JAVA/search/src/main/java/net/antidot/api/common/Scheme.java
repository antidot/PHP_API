/**
 * 
 */
package net.antidot.api.common;

/** URI schemes supported by Antidot Web Services.
 * <p>
 * Antidot Web services supports one or more of these schemes.
 */
public enum Scheme {
	/** HTTP: Non secured mode */
	AFS_SCHEME_HTTP("http"),
	/** HTTPS: Secured mode */
	AFS_SCHEME_HTTPS("https");

	private String value;

	private Scheme(String value) {
		this.value = value;
	}

	/** Retrieves string representation of the scheme.
	 * @return scheme as string
	 */
	public String value() {
		return this.value;
	}
}
