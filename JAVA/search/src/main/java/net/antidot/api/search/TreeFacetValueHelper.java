package net.antidot.api.search;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import net.antidot.protobuf.facets.FacetsProto.TreeNode;

/** Represent one value of a facet.
 * You can get access to main data such as:
 * - key used to filter on specific facet value,
 * - label for better integration than simple key,
 * - count of documents defining the facet value,
 * - sub-facet values for hierarchical facets.
 */
public class TreeFacetValueHelper implements FacetValueHelperInterface {

	private TreeNode treeNode;

	public TreeFacetValueHelper(TreeNode treeNode) {
		this.treeNode = treeNode;
	}
	
	/** Retrieve the key of the facet value.
	 * This key is used to filter on this specific facet value.
	 * @return key of the facet value.
	 */
	public String getKey() {
		return treeNode.getKey();
	}
	
	/** Retrieve the label of the facet value.
	 * This label can be used instead of the @a key for a better web integration.
	 * 
	 * If no label has been defined on PaF side, this method returns same result as @a getKey method.
	 * @return label of the facet value.
	 */
	public String getLabel() {
		if (treeNode.getLabelsCount() > 0) {
			return treeNode.getLabels(0).getLabel();
		} else {
			return getKey();
		}
	}
	
	/** Retrieve number of documents which defines this specific facet value.
	 * @return number of documents defining this facet value.
	 */
	public long getCount() {
		return treeNode.getItems();
	}
	
	/** List sub-values of this facet value.
	 * This list can be empty.
	 * See Antidot documentation to learn how to configure such facets.
	 * @return sub-values of the facet value.
	 */
	public List<FacetValueHelperInterface> getValues() {
		List<FacetValueHelperInterface> result = new ArrayList<FacetValueHelperInterface>();
		Iterator<TreeNode> nodeIt = treeNode.getNodeList().iterator();
		while (nodeIt.hasNext()) {
			TreeNode child = nodeIt.next();
			result.add(new TreeFacetValueHelper(child));
		}
		return result;
	}
}
