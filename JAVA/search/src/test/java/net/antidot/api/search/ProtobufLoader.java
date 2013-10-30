package net.antidot.api.search;

import java.io.FileInputStream;
import java.io.FileNotFoundException;

import static org.junit.Assert.fail;

import java.io.IOException;

import net.antidot.protobuf.reply.Reply.replies;

public abstract class ProtobufLoader {

	protected replies loadProtobuf(String filename) {
		FileInputStream proto = null;
		try {
			proto = new FileInputStream("src/test/java/net/antidot/api/search/data/" + filename);
		} catch (FileNotFoundException e) {
			fail("Cannot find test file: " + e);
		}

		try {
			return replies.parseFrom(proto, ProtobufExtensionManager.getResgistry());
		} catch (IOException e) {
			fail("Cannot parse protobuf: " + e);
		}
		return null;
	}
}
