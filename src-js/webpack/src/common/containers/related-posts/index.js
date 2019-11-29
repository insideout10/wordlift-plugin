/**
 * External dependencies
 */
import { connect } from "react-redux";

/**
 * Internal dependencies
 */
import RelatedPostsPanel from "../../components/related-posts-panel";
import { relatedPostsRequest } from "./actions";

const mapStateToProps = ({ relatedPosts }) => relatedPosts;

const mapDispatchToProps = dispatch => ({
  requestRelatedPosts: () => dispatch(relatedPostsRequest())
});

export default connect(mapStateToProps, mapDispatchToProps)(RelatedPostsPanel);
