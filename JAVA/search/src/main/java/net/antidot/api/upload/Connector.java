package net.antidot.api.upload;

import java.io.IOException;
import java.io.InputStream;
import java.net.URI;
import java.net.URISyntaxException;

import org.apache.http.HttpEntity;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.utils.URIBuilder;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.util.EntityUtils;

import net.antidot.api.common.Authentication;
import net.antidot.api.common.BadReplyException;
import net.antidot.api.common.Scheme;
import net.antidot.api.common.Service;


/** PaF upload connector.
 * <p>
 * This connector allows upload of one or more files to specific PaF through Antidot Back Office.
 */
public class Connector {
	private Scheme scheme;
	private String host;
	private Service service;
	private String pafName;
	private Authentication authentication;

	/** Constructs new PaF connector.
	 * <p>
	 * Connector is created with default URL scheme AFS_SCHEME_HTTP (see Scheme).
	 * @param host [in] host name of the server hosting Antidot Back Office.
	 * @param service [in] service to consider on the hosting server (see {@link Service}).
	 * @param pafName [in] name of the PaF for the specified service.
	 * @param authentication [in] authentication method along with user name and password.
	 */
	public Connector(String host, Service service, String pafName, Authentication authentication) {
		this(Scheme.AFS_SCHEME_HTTP, host, service, pafName, authentication);
	}

	/** Constructs new PaF connector.
	 * @param scheme [in] specific scheme to use for the connection (see {@link Scheme}).
	 * @param host [in] host name of the server hosting Antidot Back Office.
	 * @param service [in] service to consider on the hosting server (see {@link Service}).
	 * @param pafName [in] name of the PaF for the specified service.
	 * @param authentication [in] authentication method along with user name and password.
	 * @exception IllegalArgumentException when required scheme is not supported by the connector.
	 */
	public Connector(Scheme scheme, String host, Service service,
			String pafName, Authentication authentication) {
		if (scheme != Scheme.AFS_SCHEME_HTTP && scheme != Scheme.AFS_SCHEME_HTTPS) {
			throw new IllegalArgumentException("Upload connector support only HTTP and HTTPS connections");
		}
		this.scheme = scheme;
		this.host = host;
		this.service = service;
		this.pafName = pafName;
		this.authentication = authentication;
	}
	
	/** Uploads one document.
	 * @param doc [in] document to upload.
	 * @return {@link Reply} object.
	 * @throws ClientProtocolException in case of an http protocol error.
	 * @throws IOException in case of a problem or the connection was aborted.
	 * @throws URISyntaxException connector should have been initialized with bad host name.
	 * @throws FileUploadException with detailed error.
	 */
	public Reply uploadDocument(DocumentInterface doc) throws IOException, URISyntaxException {
		return this.uploadDocument(doc, null);
	}

	/** Uploads one document specifying upload details.
	 * @param doc [in] document to upload.
	 * @param comment [in] details associated to the upload.
	 * @return {@link Reply} object.
	 * @throws ClientProtocolException in case of an http protocol error.
	 * @throws IOException in case of a problem or the connection was aborted.
	 * @throws URISyntaxException connector should have been initialized with bad host name.
	 * @throws FileUploadException with detailed error.
	 */
	public Reply uploadDocument(DocumentInterface doc, String comment) throws IOException, URISyntaxException {
		DocumentManager mgr = new DocumentManager();
		mgr.addDocument(doc);
		return this.uploadDocuments(mgr, comment);
	}

	/** Uploads one or more documents using {@link DocumentManager}.
	 * @param mgr [in] document manager with at least one document.
	 * @return {@link Reply} object.
	 * @throws ClientProtocolException in case of an http protocol error.
	 * @throws IOException in case of a problem or the connection was aborted.
	 * @throws URISyntaxException connector should have been initialized with bad host name.
	 * @throws FileUploadException with detailed error.
	 */
	public Reply uploadDocuments(DocumentManager mgr) throws IOException, URISyntaxException {
		return this.uploadDocuments(mgr, null);
	}
	
	/** uploads one or more documents specifying upload details.
	 * @param mgr [in] document manager with at least one document.
	 * @param comment [in] details associated to the upload.
	 * @return {@link Reply} object.
	 * @throws ClientProtocolException in case of an http protocol error.
	 * @throws IOException in case of a problem or the connection was aborted.
	 * @throws URISyntaxException connector should have been initialized with bad host name.
	 * @throws FileUploadException with detailed error.
	 */
	public Reply uploadDocuments(DocumentManager mgr, String comment) throws ClientProtocolException, IOException, URISyntaxException {
		return upload(mgr, comment);
	}
	
	
	protected Reply upload(DocumentManager mgr, String comment) throws URISyntaxException, ClientProtocolException, IOException {
		MultipartEntity entity = new MultipartEntity(mgr);
		URI uri = this.buildUri(comment);

		CloseableHttpClient httpClient = HttpClients.createDefault();
		HttpPost httpPost = new HttpPost(uri);
		httpPost.setEntity(entity);
		httpPost.setHeader("Connection", "close");
		httpPost.setHeader("Accept", "application/json");

		return buildReply(httpClient.execute(httpPost));
	}
	
	protected Reply buildReply(CloseableHttpResponse response) throws IOException {
		try {
		    HttpEntity replyEntity = response.getEntity();
		    if (replyEntity != null) {
		        InputStream inStream = replyEntity.getContent();
		        try {
		        	return Reply.createReply(EntityUtils.toString(replyEntity));
		        } finally {
		            inStream.close();
		        }
		    } else {
		    	throw new BadReplyException("No reply available");
		    }
		} finally {
		    response.close();
		}
	}

	private URI buildUri(String comment) throws URISyntaxException {
		URIBuilder uriBuilder = new URIBuilder()
							  .setScheme(this.scheme.value())
							  .setHost(this.host)
							  .setPath(this.buildPath())
							  .setParameter("afs:login", this.buildAuthentication());
		if (comment != null) {
			uriBuilder.setParameter("comment", comment);
		}
		return uriBuilder.build();
	}

	private String buildPath() {
		return "/bo-ws/service/" + this.service.getId() + "/instance/" + 
				this.service.getStatus() + "/paf/" + this.pafName + "/upload";
	}

	private String buildAuthentication() {
		return "login://" + this.authentication.getUser() + ":" + 
				this.authentication.getPassword() + "@" + this.authentication.getAuthority();
	}
}
