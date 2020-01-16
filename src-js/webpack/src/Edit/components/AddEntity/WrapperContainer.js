import { connect } from "react-redux";

import { close } from "./actions";
import Wrapper from "./Wrapper";

const mapStateToProps = state => ({ open: state.open });

const mapDispatchToProps = dispatch => ({
  onBlur: () => dispatch(close())
});

export default connect(mapStateToProps, mapDispatchToProps)(Wrapper);
