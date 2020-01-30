/**
 * TransformFunctionField : it displays the list of transformation functions for the single property.
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
import SelectComponent from "../../select-component";

class _TransformFunctionField extends React.Component {
  render() {
    return (
      <tr>
        <td colSpan="2">Transform Function</td>
        <td colSpan="3">
          <SelectComponent
              className="wl-table__select-field"
            options={this.props.transformHelpTextOptions}
            value={this.props.propData.transformHelpText}
            onChange={event => {
              PROPERTY_DATA_CHANGED_ACTION.payload = {
                fieldKey: "transformHelpText",
                value: event.target.value,
                propertyId: this.props.propData.property_id
              };
              this.props.dispatch(PROPERTY_DATA_CHANGED_ACTION);
            }}
          />
        </td>
      </tr>
    );
  }
}

export const TransformFunctionField = connect()(_TransformFunctionField);
