import { connect } from "react-redux";

import ContentClassificationPanel from "./ContentClassificationPanel";

const mapStateToProps = ({ entities }) => ({ entities });

export default connect(mapStateToProps)(ContentClassificationPanel);
