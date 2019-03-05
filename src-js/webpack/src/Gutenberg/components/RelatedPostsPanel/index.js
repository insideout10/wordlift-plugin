import { connect } from "react-redux";

import RelatedPostsPanel from "./RelatedPostsPanel";

const mapStateToProps = ({ relatedPosts }) => ({ relatedPosts });

export default connect(mapStateToProps)(RelatedPostsPanel);
