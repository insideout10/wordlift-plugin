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
    this.handleChangeForPropertyField = this.handleChangeForPropertyField.bind(this);
    this.getInputFieldForFieldName = this.getInputFieldForFieldName.bind(this);
  }
  /**
   * When a property item changes this method gets fired
   * @param {String} fieldKey Field Key is the key present in property data
   * @param {Object} event The onChange event when a input field is changed
   */
  handleChangeForPropertyField(fieldKey, event) {
    const action = PROPERTY_DATA_CHANGED_ACTION;
    action.payload = {
      fieldKey: fieldKey,
      value: event.target.value,
      propertyId: this.props.propData.property_id
    };
    this.props.dispatch(action);
  }

  /**
   * Display a list of options or just a text box depends on the field type
   */
  getInputFieldForFieldName() {
    const field_type = this.props.propData.fieldTypeHelpText;
    const results = this.props.fieldNameOptions.filter(el => el.field_type === field_type);
    const value = results.length > 0 ? results[0].value : null;
    // If the value is array then display a selection box
    if (Array.isArray(value)) {
      return (
        <SelectComponent
          inputDataIsOptionGroup={field_type === "acf"}
          className="wl-form-select"
          options={value}
          value={this.props.propData.fieldHelpText}
          onChange={event => {
            this.handleChangeForPropertyField("fieldHelpText", event);
          }}
        />
      );
    } else {
      return (
        <PropertyInputField
          propData={this.props.propData}
          handleChangeForPropertyField={this.handleChangeForPropertyField}
          inputKeyName="fieldHelpText"
        />
      );
    }
  }

  render() {
    return (
      <React.Fragment>
        <a className="row-title">{this.props.propData.propertyHelpText}</a>
        <br />
        <table className="wl-container wl-container-full wl-spaced-table wl-property-edit-item">
          <tbody>
            <PropertyNameField {...this.props}/>
            <FieldTypeField {...this.props}/>
            <tr>
              <td colspan="2">Field Text</td>
              <td colspan="3">{this.getInputFieldForFieldName()}</td>
            </tr>
            <tr>
              <td colspan="2">Transform Function</td>
              <td colspan="3">
                <SelectComponent
                  className="wl-form-select"
                  options={this.props.transformHelpTextOptions}
                  value={this.props.propData.transformHelpText}
                  onChange={event => {
                    this.handleChangeForPropertyField("transformHelpText", event);
                  }}
                ></SelectComponent>
              </td>
            </tr>
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
