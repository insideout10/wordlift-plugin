import { connect } from "react-redux";

import ContentClassificationPanel from "./ContentClassificationPanel";

const mapStateToProps = ({ entities, processingBlocks }) => ({ entities, processingBlocks });

export default connect(mapStateToProps)(ContentClassificationPanel);
