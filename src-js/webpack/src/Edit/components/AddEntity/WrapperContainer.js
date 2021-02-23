import { connect } from "react-redux";

import Wrapper from "./Wrapper";

const mapStateToProps = state => ({ open: state.open });

export default connect(mapStateToProps)(Wrapper);
