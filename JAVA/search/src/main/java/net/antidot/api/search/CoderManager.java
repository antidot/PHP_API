package net.antidot.api.search;

/** Entry point for feed and filter coders.
 */
public class CoderManager {
	private FeedCoderInterface feedCoder;
	private FilterCoderInterface filterCoder;

	/** Constructs new coder manager.
	 * Default implementation for feed and filter coders are instantiated.
	 */
	public CoderManager() {
		this(new DefaultFeedCoder(), new DefaultFilterCoder());
	}
	
	/** Constructs new coder manager with specific feed coder.
	 * Default implementation for filter coder is instantiated.
	 * @param feedCoder [in] specific feed coder instance.
	 */
	public CoderManager(FeedCoderInterface feedCoder) {
		this(feedCoder, new DefaultFilterCoder());
	}
	
	/** Constructs new coder manager with specific filter coder.
	 * Default implementation for feed coder is instantiated.
	 * @param filterCoder [in] specific filter coder instance.
	 */
	public CoderManager(FilterCoderInterface filterCoder) {
		this(new DefaultFeedCoder(), filterCoder);
	}

	/** Constructs new coder manager with specific feed and filter coders.
	 * @param feedCoder [in] specific feed coder instance.
	 * @param filterCoder [in] specific filter coder instance.
	 */
	public CoderManager(FeedCoderInterface feedCoder,
			FilterCoderInterface filterCoder) {
		this.feedCoder = feedCoder;
		this.filterCoder = filterCoder;
	}

	/** Retrieves feed coder.
	 * @return feed coder.
	 */
	public FeedCoderInterface getFeedCoder() {
		return feedCoder;
	}

	/** Retrieves filter coder.
	 * @return filter coder.
	 */
	public FilterCoderInterface getFilterCoder() {
		return filterCoder;
	}
}
