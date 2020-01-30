/**
 * FieldNameField : it displays the field name field in the edit mappings ui
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
import {PropertyInputField} from "./property-input-field";


class _FieldNameField extends React.Component {
  constructor(props) {
    super(props);
    this.handleChangeForPropertyField = this.handleChangeForPropertyField.bind(this);
  }
  /**
   * When a property item changes this method gets fired
   * @param {String} fieldKey Field Key is the key present in property data
   * @param {Object} event The onChange event when a input field is changed
   */
  handleChangeForPropertyField(fieldKey, event) {
    PROPERTY_DATA_CHANGED_ACTION.payload = {
      fieldKey: fieldKey,
      value: event.target.value,
      propertyId: this.props.propData.property_id
    };
    this.props.dispatch(PROPERTY_DATA_CHANGED_ACTION);
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
      <tr>
        <td colSpan="2">Field Text</td>
        <td colSpan="3">{this.getInputFieldForFieldName()}</td>
      </tr>
    );
  }
}

export const FieldNameField = connect()(_FieldNameField);
