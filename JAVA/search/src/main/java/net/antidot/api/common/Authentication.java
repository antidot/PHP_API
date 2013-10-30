package net.antidot.api.common;

/** Authentication object necessary for various Antidot Web Services.
 * <p>
 * This object stores user name, password and authority used for authentication.
 */
public class Authentication {
	private String user;
	private String password;
	private Authorities authority;

	/** Constructs new authentication instance.
	 * @param user [in] login user name.
	 * @param password [in] password.
	 * @param authority [in] authentication authority to use {@link Authorities}
	 */
	public Authentication(String user, String password, Authorities authority) {
		this.user = user;
		this.password = password;
		this.authority = authority;
	}

	/** Retrieves user name.
	 * @return login user name.
	 */
	public String getUser() {
		return this.user;
	}

	/** Retrieves password.
	 * @return password.
	 */
	public String getPassword() {
		return this.password;
	}

	/** Retrieves authentication authority.
	 * @return authority to use for authentication.
	 */
	public String getAuthority() {
		return this.authority.value();
	}
}