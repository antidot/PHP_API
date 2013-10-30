package net.antidot.api.upload;

import java.io.IOException;
import java.io.StringReader;
import java.util.UUID;

import com.google.gson.stream.JsonReader;

/** Reply of uploaded documents.
 * <p>
 * This reply is returned when documents are uploaded to specific PaF through Antidot Back Office. 
 */
public class Reply {

	private int jobId;
	private boolean started;
	private UUID uuid;

	
	/** Constructs new reply object from upload web service reply of Antidot Back Office.
	 * @param json [in] reply of the web service.
	 * @return Reply object.
	 * @throws IOException bad error occurred.
	 * @throws FileUploadException when parameters provided to connector does not match any registered PaF.
	 */
	static public Reply createReply(String json) throws IOException {
		return new Reply(json);
	}
	
	private Reply(String json) throws IOException {
		JsonReader reader = new JsonReader(new StringReader(json));
		try {
			readReply(reader);
		} finally {
			reader.close();
		}
	}

	private void readReply(JsonReader reader) throws IOException {
		reader.beginObject();
		while (reader.hasNext()) {
			String name = reader.nextName();
			if (name.equals("result")) {
				buildResult(reader);
			} else if (name.equals("error")) {
				launchError(reader);
			} else {
				reader.skipValue();
			}
		}
		reader.endObject();
	}

	private void launchError(JsonReader reader) throws IOException {
		long errorCode = -1;
		String description = null;
		String details = null;

		reader.beginObject();
		while (reader.hasNext()) {
			String name = reader.nextName();
			if (name.equals("code")) {
				errorCode = reader.nextLong();
			} else if (name.equals("description")) {
				description = reader.nextString();
			} else if (name.equals("details")) {
				details = reader.nextString();
			} else {
				reader.skipValue();
			}
		}
		reader.endObject();
		throw new FileUploadException(errorCode, description, details);
	}

	private void buildResult(JsonReader reader) throws IOException {
		reader.beginObject();
		while (reader.hasNext()) {
			String name = reader.nextName();
			if (name.equals("jobId")) {
				this.jobId = reader.nextInt();
			} else if (name.equals("started")) {
				this.started = reader.nextBoolean();
			} else if (name.equals("uuid")) {
				this.uuid = UUID.fromString(reader.nextString());
			} else {
				reader.skipValue();
			}
		}
		reader.endObject();
	}

	/** Retrieves job identifier corresponding to the uploaded document(s).
	 * <p>
	 * This job id could be used to check job status.
	 * @return job id.
	 */
	public int getJobId() {
		return jobId;
	}

	/** Checks whether the job has started.
	 * @return true when the job has started, false otherwise.
	 */
	public boolean isStarted() {
		return started;
	}

	/** Retrieves job UUID.
	 * This job UUID could be used to check job status.
	 * @return UUID of the job.
	 */
	public UUID getUuid() {
		return uuid;
	}
}