package net.antidot.api.search;

import static org.junit.Assert.*;

import java.util.List;

import net.antidot.common.lang.LangProtos.Lang;
import net.antidot.common.lang.RegionProtos.Region;
import net.antidot.protobuf.facets.FacetsProto.Interval;
import net.antidot.protobuf.facets.FacetsProto.TreeNode;
import net.antidot.protobuf.lang.Label.Language;
import net.antidot.protobuf.lang.Label.LocalizedLabel;

import org.junit.Test;

public class FacetValueTest {

	@Test
	public void testSimpleGetter() {
		String key = "foo";
		String label = "bar";
		long count = 42;
		Interval interval = Interval.newBuilder()
				.setKey(key)
				.setItems(count)
				.addLabels(LocalizedLabel.newBuilder()
						.setLabel(label)
						.setLanguage(Language.newBuilder()
								.setLang(Lang.AA)
								.setRegion(Region.AC))).build();
		IntervalFacetValueHelper facetValue = new IntervalFacetValueHelper(interval);
		
		assertEquals(key, facetValue.getKey());
		assertEquals(label, facetValue.getLabel());
		assertEquals(count, facetValue.getCount());
	}

	@Test
	public void testSimpleGetterWithoutLabel() {
		String key = "foo";
		long count = 42;
		Interval interval = Interval.newBuilder()
				.setKey(key)
				.setItems(count)
				.build();
		IntervalFacetValueHelper facetValue = new IntervalFacetValueHelper(interval);
		
		assertEquals(key, facetValue.getKey());
		assertEquals(key, facetValue.getLabel());
		assertEquals(count, facetValue.getCount());
	}
	
	@Test
	public void testTreeFacetValue() {
		String key = "bar";
		long count = 666;
		String subKey = "foo";
		long subCount = 42;
		TreeNode node = TreeNode.newBuilder()
				.setKey(key)
				.setItems(count)
				.addNode(TreeNode.newBuilder()
						.setKey(subKey)
						.setItems(subCount)
						.build())
				.build();
						
		TreeFacetValueHelper value = new TreeFacetValueHelper(node);
		
		assertEquals(key, value.getKey());
		assertEquals(count, value.getCount());

		List<FacetValueHelperInterface> values = value.getValues();
		assertEquals(1, values.size());
		FacetValueHelperInterface firstValue = values.get(0);
		assertEquals(subKey, firstValue.getKey());
		assertEquals(subCount, firstValue.getCount());
	}
}
