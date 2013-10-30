package net.antidot.api.search;

import net.antidot.protobuf.facets.FacetsProto;
import net.antidot.protobuf.reply.ReplySetProto;

import com.google.protobuf.ExtensionRegistry;

/** Simple manager which registers necessary Google extensions.
 */
public class ProtobufExtensionManager {
	private static ExtensionRegistry registry = null;

	/** Retrieves Google protobuf registry to be used when loading protobuf from bytes/string.
	 * @return Googleprotobuf registry.
	 */
	public static ExtensionRegistry getResgistry() {
		if (registry == null) {
			intializeRegistry();
		}
		return registry;
	}
	
	private static void intializeRegistry() {
		registry = ExtensionRegistry.newInstance();
		ReplySetProto.registerAllExtensions(registry);
		FacetsProto.registerAllExtensions(registry);
	}
}
