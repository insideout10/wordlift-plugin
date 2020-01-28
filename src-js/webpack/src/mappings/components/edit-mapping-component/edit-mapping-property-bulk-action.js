/**
 * EditMappingPropertyBulkAction : it handles the bulk action for the property list in the edit
 * mapping screen.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import { connect } from "react-redux";

/**
 * Internal dependencies.
 */
import BulkActionComponent from "../bulk-action-component";
import { BULK_ACTION_SELECTION_CHANGED_ACTION, PROPERTY_ITEMS_BULK_ACTION } from "../../actions/actions";

class _EditMappingPropertyBulkAction extends React.Component {
  constructor(props) {
    super(props);
    this.bulkActionSubmitHandler = this.bulkActionSubmitHandler.bind(this);
    this.bulkActionOptionChangedHandler = this.bulkActionOptionChangedHandler.bind(this);
  }

  bulkActionSubmitHandler() {
    this.props.dispatch(PROPERTY_ITEMS_BULK_ACTION);
  }

  bulkActionOptionChangedHandler(event) {
    const selectedBulkOption = event.target.value;
    BULK_ACTION_SELECTION_CHANGED_ACTION.payload = {
      selectedBulkAction: selectedBulkOption
    };
    this.props.dispatch(BULK_ACTION_SELECTION_CHANGED_ACTION);
  }

  render() {
    return (
      <BulkActionComponent
        chosenCategory={this.props.chosenCategory}
        bulkActionSubmitHandler={this.bulkActionSubmitHandler}
        bulkActionOptionChangedHandler={this.bulkActionOptionChangedHandler}
      />
    );
  }
}

export const EditMappingPropertyBulkAction = connect(state => ({
  chosenCategory: state.PropertyListData.chosenPropertyCategory
}))(_EditMappingPropertyBulkAction);
