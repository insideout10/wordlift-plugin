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
import PropertyComponent from "./index";
import PropertyListItemComponent from "./property-list-item-component";
import { OPEN_OR_CLOSE_PROPERTY_ACTION } from "../../../actions/actions";
import { AddPropertyButton } from "./add-property-button";
import { PropertyCategories } from "./property-categories";
import { PropertyHeaderRow } from "./property-header-row";
import { PropertyNoItemMessage } from "./property-no-item-message";
import { PropertyItemCheckbox } from "./property-item-checkbox";
import { WlContainer } from "../../../blocks/wl-container";
import {WlTable} from "../../../blocks/wl-table";

class PropertyListComponent extends React.Component {
  constructor(props) {
    super(props);
    this.switchState = this.switchState.bind(this);
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
        <PropertyCategories {...this.props} />
        <br />
        <WlContainer fullWidth={true}>
          <WlTable bottomAligned={true}>
            <PropertyHeaderRow {...this.props} />
            <tbody>
              <PropertyNoItemMessage {...this.props} />
              {this.props.propertyList
                .filter(property => property.propertyStatus === this.props.chosenCategory)
                .map((property, index) => {
                  return (
                    <tr key={index} className="wl-property-list-item-container">
                      <PropertyItemCheckbox property={property} />
                      <td>{this.renderListComponentBasedOnState(property, index)}</td>
                      <td />
                    </tr>
                  );
                })}
              <AddPropertyButton />
            </tbody>
          </WlTable>
        </WlContainer>
      </React.Fragment>
    );
  }
}

PropertyListComponent.propTypes = {
  propertyList: PropTypes.array
};

const mapStateToProps = function(state) {
  return {
    propertyHeaderCheckboxClicked: state.PropertyListData.propertyHeaderCheckboxClicked,
    propertyList: state.PropertyListData.propertyList,
    chosenCategory: state.PropertyListData.chosenPropertyCategory
  };
};

export default connect(mapStateToProps)(PropertyListComponent);
