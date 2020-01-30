/**
 * PropertyNameField : it displays the property name field in the edit mappings ui
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";
import { connect } from "react-redux";

/**
 * Internal dependencies.
 */
import { PROPERTY_DATA_CHANGED_ACTION } from "../../../actions/actions";
import {PropertyInputField} from "./property-input-field";

class _PropertyNameField extends React.Component {
  render() {
    return (
      <tr>
        <td colSpan="2">Property Name</td>
        <td colSpan="3">
          <PropertyInputField
            propData={this.props.propData}
            handleChangeForPropertyField={(fieldKey, event) => {
              PROPERTY_DATA_CHANGED_ACTION.payload = {
                fieldKey: fieldKey,
                value: event.target.value,
                propertyId: this.props.propData.property_id
              };
              this.props.dispatch(PROPERTY_DATA_CHANGED_ACTION);
            }}
            inputKeyName="propertyHelpText"
          />
        </td>
      </tr>
    );
  }
}

export const PropertyNameField = connect()(_PropertyNameField);
