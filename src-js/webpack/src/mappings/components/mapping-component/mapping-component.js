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
import MappingListItemComponent from "./mapping-list-item-component";
import MappingComponentHelper from "./mapping-component-helper";
import {
  BULK_ACTION_SELECTION_CHANGED_ACTION,
  MAPPING_ITEMS_BULK_APPLY_ACTION,
  MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION,
  MAPPING_LIST_SORT_TITLE_CHANGED_ACTION,
  MAPPINGS_REQUEST_ACTION,
} from "../../actions/actions";
import { connect } from "react-redux";
import CategoryComponent, { ACTIVE_CATEGORY } from "../category-component";
import BulkActionComponent from "../bulk-action-component";
import {
  AddNewButton,
  MappingHeaderRow,
  MappingNoActiveItemMessage,
} from "./mapping-list-subcomponents";

// Set a reference to the WordLift's Mapping settings stored in the window instance.
const mappingSettings = window["wlMappingsConfig"] || {};

class MappingComponent extends React.Component {
  constructor(props, context, updateMappingItems) {
    super(props, context);
    this.categorySelectHandler = this.categorySelectHandler.bind(this);
    this.bulkActionOptionChangedHandler = this.bulkActionOptionChangedHandler.bind(this);
    this.sortMappingItemsByTitle = this.sortMappingItemsByTitle.bind(this);
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
   * Sorts the mapping items by title either ascending or descending
   * depending on the current state.
   */
  sortMappingItemsByTitle() {
    this.props.dispatch(MAPPING_LIST_SORT_TITLE_CHANGED_ACTION);
  }
  /**
   * Fetch the mapping items from api.
   * @return void
   */
  getMappingItems() {
    this.props.dispatch( MAPPINGS_REQUEST_ACTION );
  }
  /**
   * When the category is selected in the categoryComponent this method
   * is fired.
   * @param {String} category The category choosen by the user
   * @return void
   */
  categorySelectHandler(category) {
    const action = MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION;
    action.payload = {
      categoryName: category
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
      <React.Fragment>
        <AddNewButton />
        <CategoryComponent
          source={this.props.mappingItems}
          categoryKeyName="mappingStatus"
          categories={["active", "trash"]}
          categorySelectHandler={this.categorySelectHandler}
          choosenCategory={this.props.chosenCategory}
        />
        <br />
        <table className="wp-list-table widefat striped wl-table">
          <thead>
            <MappingHeaderRow/>
          </thead>
          <tbody>
            <MappingNoActiveItemMessage {...this.props} />
            {this.props.mappingItems
              .filter(el => el.mappingStatus === this.props.chosenCategory)
              .map((item, index) => {
                return (
                  <MappingListItemComponent
                    key={index}
                    nonce={mappingSettings.wl_edit_mapping_nonce}
                    mappingData={item}
                  />
                );
              })}
          </tbody>
          <tfoot>
            <MappingHeaderRow/>
          </tfoot>
        </table>
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
