import { connect } from "react-redux";

import { close } from "./actions";
import Wrapper from "./Wrapper";

const mapStateToProps = state => ({ open: state.open });

const mapDispatchToProps = dispatch => ({
  onBlur: () => {
    console.log("Blurring...");
    dispatch(close());
  }
});

export default connect(mapStateToProps, mapDispatchToProps)(Wrapper);
