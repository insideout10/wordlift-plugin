/**
 * PropertyComponent : used to display a individual property, has 2 states
 * allow the user to edit it and add a new property
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";

/**
 * Internal dependencies
 */
import SelectComponent from "../select-component";
import { PROPERTY_DATA_CHANGED_ACTION } from "../../actions/actions";
import {PropertyNameField} from "./property-name-field";
import {FieldTypeField} from "./field-type-field";
import {FieldNameField} from "./field-name-field";
import {TransformFunctionField} from "./transform-function-field";

export const PropertyInputField = ({ propData, handleChangeForPropertyField, inputKeyName }) => {
  return (
    <React.Fragment>
      <input
        type="text"
        className="wl-form-control"
        defaultValue={propData[inputKeyName]}
        onChange={event => {
          handleChangeForPropertyField(inputKeyName, event);
        }}
      />
    </React.Fragment>
  );
};
class PropertyComponent extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <React.Fragment>
        <a className="row-title">{this.props.propData.propertyHelpText}</a>
        <br />
        <table className="wl-container wl-container-full wl-spaced-table wl-property-edit-item">
          <tbody>
            <PropertyNameField {...this.props} />
            <FieldTypeField {...this.props} />
            <FieldNameField {...this.props} />
            <TransformFunctionField {...this.props} />
            <tr>
              <td colspan="2"></td>
              <td>
                <button
                  disabled={this.props.propData.propertyHelpText.length <= 0}
                  className="wl-close-mapping button action bg-primary text-white"
                  onClick={() => this.props.switchState(this.props.propData.property_id)}
                >
                  Close Mapping
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </React.Fragment>
    );
  }
}

// supply a property object as data
PropertyComponent.propTypes = {
  propertyData: PropTypes.object
};

const mapStateToProps = function(state) {
  return {
    transformHelpTextOptions: state.PropertyListData.transformHelpTextOptions,
    fieldTypeHelpTextOptions: state.PropertyListData.fieldTypeHelpTextOptions,
    fieldNameOptions: state.PropertyListData.fieldNameOptions
  };
};

export default connect(mapStateToProps)(PropertyComponent);
