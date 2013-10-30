package net.antidot.api.search;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import net.antidot.api.common.NotImplementedException;
import net.antidot.protobuf.facets.FacetsProto;
import net.antidot.protobuf.facets.FacetsProto.Facet;
import net.antidot.protobuf.facets.FacetsProto.FacetInterval;
import net.antidot.protobuf.facets.FacetsProto.FacetTree;
import net.antidot.protobuf.facets.FacetsProto.Interval;
import net.antidot.protobuf.facets.FacetsProto.TreeNode;

/** Simplify the build of facet values.
 * <p>
 * This class is internally used and should never be used elsewhere. 
 */
public class FacetValueBuilder {
	private Facet facet;

	/** Constructs new builder instance.
	 * @param facet [in] Google protobuf used to build facet values.
	 * @return new builder instance.
	 */
	public static FacetValueBuilder newBuilder(Facet facet) {
		return new FacetValueBuilder(facet);
	}
	
	private FacetValueBuilder(Facet facet) {
		this.facet = facet;
	}

	/** Builds the list of facet value.
	 * @return list of facet value.
	 */
	public List<FacetValueHelperInterface> build() {
		if (facet.hasExtension(FacetsProto.treeExt)) {
			return buildTreeFacet(facet.getExtension(FacetsProto.treeExt));
		} else if (facet.hasExtension(FacetsProto.intervalExt)) {
			return buildIntervalFacet(facet.getExtension(FacetsProto.intervalExt));
		} else {
			throw new NotImplementedException("Invalid/unsupported facet type");
		}
	}

	private List<FacetValueHelperInterface> buildIntervalFacet(FacetInterval facetInterval) {
		List<FacetValueHelperInterface> result = new ArrayList<FacetValueHelperInterface>();
		Iterator<Interval> intervalIt = facetInterval.getIntervalList().iterator();
		while (intervalIt.hasNext()) {
			result.add(new IntervalFacetValueHelper(intervalIt.next()));
		}
		return result;
	}

	private List<FacetValueHelperInterface> buildTreeFacet(FacetTree facetTree) {
		List<FacetValueHelperInterface> result = new ArrayList<FacetValueHelperInterface>();
		Iterator<TreeNode> nodeIt = facetTree.getNodeList().iterator();
		while (nodeIt.hasNext()) {
			result.add(new TreeFacetValueHelper(nodeIt.next()));
		}
		return result;
	}
}
