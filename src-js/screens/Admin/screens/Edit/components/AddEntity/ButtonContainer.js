import { connect } from "react-redux";

import { open } from "./actions";
import Button from "../Button";

const mapStateToProps = state => state;

const mapDispatchToProps = dispatch => ({
  onClick: () => dispatch(open())
});

export default connect(mapStateToProps, mapDispatchToProps)(Button);
