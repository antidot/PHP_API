package net.antidot.api.common;

/** Antidot Web Services authentication authorities.
 * <p>
 * List all available authentication authorities supported by Antidot Web Services.
 */
public enum Authorities {
	/** LDAP: use LDAP for authentication */
	AFS_AUTH_LDAP("LDAP"),
	/** BOWS: use internal Back Office authentication manager */
	AFS_AUTH_BOWS("BOWS"),
	/** BOWS: use internal Back Office authentication manager */
	AFS_AUTH_SSO("SSO"),
	/** ANTIDOT: internal use only */
	AFS_AUTH_ANTIDOT("Antidot");

	private String value;

	private Authorities(String value) {
		this.value = value;
	}
	
	/** Retrieves string representation of the authority.
	 * @return authority as String
	 */
	public String value() {
		return this.value;
	}
}