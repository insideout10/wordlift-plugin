/**
 * External dependencies
 */
import { connect } from "react-redux";

import Button from "../components/Button";

/**
 * @inheritDoc
 */
const mapStateToProps = state => {
  return {
    disabled: "" === state.editor.selection
  };
};

const AddEntityButton = connect(mapStateToProps)(Button);

export default AddEntityButton;
