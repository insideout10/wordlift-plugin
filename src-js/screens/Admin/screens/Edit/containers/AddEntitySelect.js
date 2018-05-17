/**
 * External dependencies
 */
import { connect } from "react-redux";

import EntitySelect from "../components/AutoCompleteEntitySelect";

/**
 * @inheritDoc
 */
const mapStateToProps = state => {
  const selection =
    "undefined" !== typeof state.editor.selection ? state.editor.selection : "";

  return {
    visible: "" !== selection,
    filter: selection
  };
};

const AddEntitySelect = connect(mapStateToProps)(EntitySelect);

export default AddEntitySelect;
