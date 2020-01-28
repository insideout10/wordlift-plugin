/**
 * PropertyListComponent : used to display list of properties present
 * in a mapping item, the user can edit, add, delete properties
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import { connect } from "react-redux";
import PropTypes from "prop-types";

/**
 * Internal dependencies
 */
import PropertyComponent from "./property-component";
import CategoryComponent from "../category-component";
import PropertyListItemComponent from "./property-list-item-component";
import {
  OPEN_OR_CLOSE_PROPERTY_ACTION,
  ADD_MAPPING_ACTION,
  PROPERTY_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION,
  PROPERTY_ITEM_SELECTED_ACTION,
  PROPERTY_ITEM_SELECT_ALL_ACTION
} from "../../actions/actions";

class PropertyListComponent extends React.Component {
  constructor(props) {
    super(props);

    this.switchState = this.switchState.bind(this);
    this.handleAddMappingClick = this.handleAddMappingClick.bind(this);
    this.categorySelectHandler = this.categorySelectHandler.bind(this);
    this.propertySelectedHandler = this.propertySelectedHandler.bind(this);
    this.selectAllPropertyHandler = this.selectAllPropertyHandler.bind(this);
    this.renderListComponentBasedOnState = this.renderListComponentBasedOnState.bind(this);
  }
  /**
   * It makes property item
   * switch from edit mode to list item mode and vice versa
   * @param {Number} propertyId
   */
  switchState(propertyId) {
    const action = OPEN_OR_CLOSE_PROPERTY_ACTION;
    action.payload = {
      propertyId: propertyId
    };
    this.props.dispatch(action);
  }
  // triggered when the add mapping button is clicked
  handleAddMappingClick() {
    this.props.dispatch(ADD_MAPPING_ACTION);
  }
  categorySelectHandler(category) {
    const action = PROPERTY_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION;
    action.payload = {
      chosenCategory: category
    };
    this.props.dispatch(action);
  }
  propertySelectedHandler(propertyId) {
    const action = PROPERTY_ITEM_SELECTED_ACTION;
    action.payload = {
      propertyId: propertyId
    };
    this.props.dispatch(action);
  }
  selectAllPropertyHandler() {
    this.props.dispatch(PROPERTY_ITEM_SELECT_ALL_ACTION);
  }
  /**
   * It Renders depends on the isOpenedOrAddedByUser boolean present
   * in the property object.
   * @param {Object} property A single property present in property list
   * @param {Number} index Index of the property in property list
   */
  renderListComponentBasedOnState(property, index) {
    if (property.isOpenedOrAddedByUser) {
      return (
        // show the property in edit mode
        <PropertyComponent propData={property} switchState={this.switchState} />
      );
    }
    // if it is not opened then return the list item
    return (
      <PropertyListItemComponent
        chosenCategory={this.props.chosenCategory}
        propData={property}
        switchState={this.switchState}
      />
    );
  }
  render() {
    return (
      <React.Fragment>
        <CategoryComponent
          source={this.props.propertyList}
          categoryKeyName="property_status"
          categories={["active", "trash"]}
          categorySelectHandler={this.categorySelectHandler}
          chosenCategory={this.props.chosenCategory}
        />
        <br />
        <table className="wp-list-table widefat striped wl-table wl-container-full">
          <thead>
            <tr>
              <th className="wl-check-column">
                <input
                  type="checkbox"
                  checked={this.props.propertyHeaderCheckboxClicked}
                  onClick={() => {
                    this.selectAllPropertyHandler();
                  }}
                />
              </th>
              <th style={{ width: "30%" }}>
                <b>Property</b>
              </th>
              <th>
                <b>Field</b>
              </th>
            </tr>
          </thead>
          <tbody>
            {0 ===
              this.props.propertyList.filter(property => property.property_status === this.props.chosenCategory)
                .length && (
              <tr>
                <td colSpan={2} className="text-center">
                  No Active properties present, click on add new
                </td>
              </tr>
            )}
            {this.props.propertyList
              .filter(property => property.property_status === this.props.chosenCategory)
              .map((property, index) => {
                return (
                  <tr key={index} className="wl-property-list-item-container">
                    <td className="wl-check-column">
                      <input
                        type="checkbox"
                        checked={property.isSelectedByUser}
                        onClick={() => {
                          this.propertySelectedHandler(property.property_id);
                        }}
                      />
                    </td>
                    <td>{this.renderListComponentBasedOnState(property, index)}</td>
                    <td />
                  </tr>
                );
              })}
            <tr className="wl-text-right">
              <td colSpan="3">
                <br />
                <button
                  className="button action bg-primary text-white wl-add-mapping"
                  style={{ margin: "auto" }}
                  onClick={this.handleAddMappingClick}
                >
                  Add Mapping
                </button>{" "}
                <br />
              </td>
            </tr>
          </tbody>
        </table>
      </React.Fragment>
    );
  }
}

PropertyListComponent.propTypes = {
  propertyList: PropTypes.array
};

const mapStateToProps = function(state) {
  return {
    propertyHeaderCheckboxClicked: state.propertyHeaderCheckboxClicked,
    propertyList: state.PropertyListData.propertyList,
    chosenCategory: state.PropertyListData.choosenPropertyCategory
  };
};

export default connect(mapStateToProps)(PropertyListComponent);
