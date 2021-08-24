import {connect} from "react-redux";

import {open} from "./actions";
import Button from "../Button";

const mapStateToProps = ({ enabled, value }) => ({
  enabled,
  label: `Add ${value}`
});

const mapDispatchToProps = dispatch => ({
  onClick: () => dispatch(open())
});

export default connect(mapStateToProps, mapDispatchToProps)(Button);
