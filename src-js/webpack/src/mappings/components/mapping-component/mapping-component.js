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
  MAPPING_ITEM_CATEGORY_CHANGED_ACTION,
  MAPPING_ITEM_SELECTED_ACTION,
  MAPPING_ITEMS_BULK_ACTION,
  MAPPING_LIST_BULK_SELECT_ACTION,
  MAPPING_LIST_CHANGED_ACTION,
  MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION,
  MAPPING_LIST_SORT_TITLE_CHANGED_ACTION
} from "../../actions/actions";
import { connect } from "react-redux";
import CategoryComponent, { ACTIVE_CATEGORY } from "../category-component";
import BulkActionComponent from "../bulk-action-component";
import {
  AddNewButton,
  MappingHeaderRow,
  MappingNoActiveItemMessage,
  MappingTableCheckBox,
  MappingTableTitleSort
} from "./mapping-list-subcomponents";

// Set a reference to the WordLift's Mapping settings stored in the window instance.
const mappingSettings = window["wlMappingsConfig"] || {};

class MappingComponent extends React.Component {
  constructor(props, context, updateMappingItems) {
    super(props, context);

    this.categorySelectHandler = this.categorySelectHandler.bind(this);
    this.bulkActionOptionChangedHandler = this.bulkActionOptionChangedHandler.bind(this);
    this.selectAllMappingItems = this.selectAllMappingItems.bind(this);
    this.sortMappingItemsByTitle = this.sortMappingItemsByTitle.bind(this);
    this.switchCategory = this.switchCategory.bind(this);
    this.updateMappingItems = this.updateMappingItems.bind(this);
    this.getMappingItems = this.getMappingItems.bind(this);
    this.bulkActionSubmitHandler = this.bulkActionSubmitHandler.bind(this);
    this.duplicateMappingItems = this.duplicateMappingItems.bind(this);
    this.selectMappingItemHandler = this.selectMappingItemHandler.bind(this);
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
   * Selects all the mapping items on the currently active category
   * When triggered on the active, it selects only the active items
   */
  selectAllMappingItems() {
    this.props.dispatch(MAPPING_LIST_BULK_SELECT_ACTION);
  }

  /**
   * Sorts the mapping items by title either ascending or descending
   * depending on the current state.
   */
  sortMappingItemsByTitle() {
    this.props.dispatch(MAPPING_LIST_SORT_TITLE_CHANGED_ACTION);
  }

  switchCategory(mappingData, categoryName) {
    const action = MAPPING_ITEM_CATEGORY_CHANGED_ACTION;
    action.payload = {
      mappingId: mappingData.mapping_id,
      mappingCategory: categoryName
    };
    this.props.dispatch(action);
    // Save Changes to the db
    mappingData.mapping_status = categoryName;
    this.updateMappingItems([mappingData]);
  }
  // Updates or deletes the mapping items based on the request
  updateMappingItems(mappingItems, type = "PUT") {
    fetch(mappingSettings.rest_url, {
      method: type,
      headers: {
        "content-type": "application/json",
        "X-WP-Nonce": mappingSettings.wl_mapping_nonce
      },
      body: JSON.stringify({
        mapping_items: MappingComponentHelper.applyApiFilters(mappingItems)
      })
    }).then(response =>
      response.json().then(data => {
        // Refresh the screen with the cloned mapping item.
        this.getMappingItems();
      })
    );
  }

  /**
   *
   * @param {Array|Object} mappingItems accepts a single
   * mapping item object or multiple mapping items, clone them by posting
   * to the api endpoint and then refresh the current list.
   */
  duplicateMappingItems(mappingItems) {
    // If single item is given, construct it to array
    mappingItems = Array.isArray(mappingItems) ? mappingItems : [mappingItems];
    fetch(mappingSettings.rest_url + "/clone", {
      method: "POST",
      headers: {
        "content-type": "application/json",
        "X-WP-Nonce": mappingSettings.wl_mapping_nonce
      },
      body: JSON.stringify({ mappingItems: mappingItems })
    }).then(response =>
      response.json().then(data => {
        // Refresh the screen with the cloned mapping item.
        this.getMappingItems();
      })
    );
  }
  /**
   * Fetch the mapping items from api.
   * @return void
   */
  getMappingItems() {
    this.props.dispatch({ type: "MAPPINGS_REQUEST" });
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
   * Called when a mapping item is clicked.
   * @param {Object} mappingData Object represeting single mapping item
   * @return void
   */
  selectMappingItemHandler(mappingData) {
    const action = MAPPING_ITEM_SELECTED_ACTION;
    action.payload = {
      mappingId: mappingData.mapping_id
    };
    this.props.dispatch(action);
  }
  bulkActionSubmitHandler() {
    MAPPING_ITEMS_BULK_ACTION.payload = {
      duplicateCallBack: this.duplicateMappingItems,
      updateCallBack: this.updateMappingItems
    };
    this.props.dispatch(MAPPING_ITEMS_BULK_ACTION);
  }
  render() {
    return (
      <React.Fragment>
        <AddNewButton />
        <CategoryComponent
          source={this.props.mappingItems}
          categoryKeyName="mapping_status"
          categories={["active", "trash"]}
          categorySelectHandler={this.categorySelectHandler}
          choosenCategory={this.props.chosenCategory}
        />
        <br />
        <table className="wp-list-table widefat striped wl-table">
          <thead>
            <MappingHeaderRow
              headerCheckBoxSelected={this.props.headerCheckBoxSelected}
              selectAllMappingsHandler={this.selectAllMappingItems}
              sortMappingItemsByTitleHandler={this.sortMappingItemsByTitle}
            />
          </thead>
          <tbody>
            <MappingNoActiveItemMessage {...this.props} />
            {this.props.mappingItems
              .filter(el => el.mapping_status === this.props.chosenCategory)
              .map((item, index) => {
                return (
                  <MappingListItemComponent
                    key={index}
                    selectMappingItemHandler={this.selectMappingItemHandler}
                    mappingIndex={index}
                    duplicateMappingItemHandler={this.duplicateMappingItems}
                    deleteMappingItemHandler={this.updateMappingItems}
                    switchCategoryHandler={this.switchCategory}
                    nonce={mappingSettings.wl_edit_mapping_nonce}
                    mappingData={item}
                  />
                );
              })}
          </tbody>
          <tfoot>
            <MappingHeaderRow
              headerCheckBoxSelected={this.props.headerCheckBoxSelected}
              selectAllMappingsHandler={this.selectAllMappingItems}
              sortMappingItemsByTitleHandler={this.sortMappingItemsByTitle}
            />
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
