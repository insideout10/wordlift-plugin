/**
 * External dependencies
 */
import { connect } from "react-redux";

import EntitySelect from "../components/AutoCompleteEntitySelect";

/**
 * @inheritDoc
 */
const mapStateToProps = state => {
  return {
    visible: "" !== state.editor.selection,
    filter:
      "undefined" !== typeof state.editor.selection
        ? state.editor.selection
        : ""
  };
};

const AddEntitySelect = connect(mapStateToProps)(EntitySelect);

export default AddEntitySelect;
