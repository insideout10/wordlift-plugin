/**
 * MappingBulkAction : it displays the bulk actions for the mapping items.
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
import { BULK_ACTION_SELECTION_CHANGED_ACTION, MAPPING_ITEMS_BULK_APPLY_ACTION } from "../../actions/actions";
import { WlContainer } from "../../blocks/wl-container";

/**
 * Class to render the bulk action on mapping screen.
 */
class _MappingBulkAction extends React.Component {
  constructor(props) {
    super(props);
    this.bulkActionOptionChangedHandler = this.bulkActionOptionChangedHandler.bind(this);
    this.bulkActionSubmitHandler = this.bulkActionSubmitHandler.bind(this);
  }
  /**
   * When the bulk option is changed, dispatch it to store.
   * @param event
   */
  bulkActionOptionChangedHandler(event) {
    const action = BULK_ACTION_SELECTION_CHANGED_ACTION;
    action.payload = {
      selectedBulkOption: event.target.value
    };
    this.props.dispatch(action);
  }
  /**
   * When the bulk action is submitted this handler is called.
   */
  bulkActionSubmitHandler() {
    this.props.dispatch(MAPPING_ITEMS_BULK_APPLY_ACTION);
  }
  render() {
    return (
      <WlContainer>
        <BulkActionComponent
          chosenCategory={this.props.chosenCategory}
          bulkActionOptionChangedHandler={this.bulkActionOptionChangedHandler}
          bulkActionSubmitHandler={this.bulkActionSubmitHandler}
        />
      </WlContainer>
    );
  }
}

export const MappingBulkAction = connect(state => ({
  chosenCategory: state.chosenCategory
}))(_MappingBulkAction);
