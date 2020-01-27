/**
 * MappingComponent : it displays the entire mapping screen
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react";

/**
 * Internal dependencies
 */
import {
  BULK_ACTION_SELECTION_CHANGED_ACTION,
  MAPPING_ITEMS_BULK_APPLY_ACTION,
  MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION,
  MAPPINGS_REQUEST_ACTION,
} from "../../actions/actions";
import { connect } from "react-redux";
import BulkActionComponent from "../bulk-action-component";
import {
  AddNewButton,
} from "./mapping-list-subcomponents";
import {MappingTable} from "./mapping-table";
import {MappingCategories} from "./mapping-categories";

class MappingComponent extends React.Component {
  constructor(props, context, updateMappingItems) {
    super(props, context);
    this.bulkActionOptionChangedHandler = this.bulkActionOptionChangedHandler.bind(this);
    this.getMappingItems = this.getMappingItems.bind(this);
    this.bulkActionSubmitHandler = this.bulkActionSubmitHandler.bind(this);
  }

  componentDidMount() {
    this.getMappingItems();
  }

  bulkActionOptionChangedHandler(event) {
    const action = BULK_ACTION_SELECTION_CHANGED_ACTION;
    action.payload = {
      selectedBulkOption: event.target.value
    };
    this.props.dispatch(action);
  }

  /**
   * Fetch the mapping items from api.
   * @return void
   */
  getMappingItems() {
    this.props.dispatch( MAPPINGS_REQUEST_ACTION );
  }

  /**
   * When the bulk action is submitted this handler is called.
   */
  bulkActionSubmitHandler() {
    this.props.dispatch(MAPPING_ITEMS_BULK_APPLY_ACTION);
  }
  render() {
    return (
      <React.Fragment>
        <AddNewButton />
        <MappingCategories />
        <br />
        <MappingTable {...this.props} />
        <div className="wl-container wl-container-full">
          <BulkActionComponent
            choosenCategory={this.props.chosenCategory}
            bulkActionOptionChangedHandler={this.bulkActionOptionChangedHandler}
            bulkActionSubmitHandler={this.bulkActionSubmitHandler}
          />
        </div>
      </React.Fragment>
    );
  }
}

const mapStateToProps = function(state) {
  return {
    mappingItems: state.mappingItems,
    chosenCategory: state.chosenCategory,
    stateObj: state,
    headerCheckBoxSelected: state.headerCheckBoxSelected,
    titleIconClass: state.titleIcon
  };
};

export default connect(mapStateToProps)(MappingComponent);
