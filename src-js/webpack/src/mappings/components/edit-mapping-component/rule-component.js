/**
 * RuleComponent : Displays a single rule
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */
/**
 * External dependencies
 */
import React from "react";
import { connect } from "react-redux";
/**
 * Internal dependencies
 */
import SelectComponent from "../select-component";
import { CHANGE_RULE_FIELD_VALUE_ACTION } from "../../actions/actions";
import { AddRuleButton } from "./add-rule-button";
import { DeleteRuleButton } from "./delete-rule-button";
import { WlColumn } from "../../blocks/wl-column";
import { WlContainer } from "../../blocks/wl-container";
import { EDIT_MAPPING_REQUEST_TERMS } from "../../actions/action-types";

class RuleComponent extends React.Component {
  constructor(props) {
    super(props);
    this.handleSelectFieldChange = this.handleSelectFieldChange.bind(this);
    this.getOptionsFromApiSource = this.getOptionsFromApiSource.bind(this);
  }

  /**
   * Saves when a change occur to selection field.
   *
   * @param {Object} event When selection field inside rule changes this event is emiited.
   * @param {String} fieldKey FieldKey indicates the selection field name
   */
  handleSelectFieldChange(event, fieldKey) {
    const action = CHANGE_RULE_FIELD_VALUE_ACTION;
    action.payload = {
      value: event.target.value,
      ruleIndex: this.props.ruleIndex,
      ruleGroupIndex: this.props.ruleGroupIndex,
      fieldKey: fieldKey
    };
    if (fieldKey === "ruleFieldOneValue") {
      // We might need to get terms when this field changes.
      this.getOptionsFromApiSource(event.target.value);
    }
    this.props.dispatch(action);
  }

  /**
   * Fetches the options for the selected rule field one
   * @param selectedOptionValue The rule field option value selected by the user.
   */
  getOptionsFromApiSource(selectedOptionValue) {
    // The store determines whether to get the data from api.
    this.props.dispatch({
      type: EDIT_MAPPING_REQUEST_TERMS,
      payload: {value: selectedOptionValue}
    });
  }

  render() {
    return (
      <WlContainer>
        <WlColumn>
          <SelectComponent
            options={this.props.ruleFieldOneOptions}
            value={this.props.ruleProps.ruleFieldOneValue}
            onChange={e => {
              this.handleSelectFieldChange(e, "ruleFieldOneValue");
            }}
            className="wl-field-one-select wl-form-select"
          />
        </WlColumn>
        <WlColumn>
          <SelectComponent
            options={this.props.ruleLogicFieldOptions}
            value={this.props.ruleProps.ruleLogicFieldValue}
            onChange={e => {
              this.handleSelectFieldChange(e, "ruleLogicFieldValue");
            }}
            className="wl-field-logic wl-form-select"
          />
        </WlColumn>
        <WlColumn>
          <SelectComponent
            options={this.props.ruleFieldTwoOptions.filter(
                el => el.parentValue === this.props.ruleProps.ruleFieldOneValue
            )}
            value={this.props.ruleProps.ruleFieldTwoValue}
            onChange={e => {
              this.handleSelectFieldChange(e, "ruleFieldTwoValue");
            }}
            className="wl-field-two-select wl-form-select"
            inputDataIsOptionGroup={this.props.ruleProps.ruleFieldOneValue === 'post_taxonomy' ? true : false}
          />
        </WlColumn>
        <AddRuleButton {...this.props} />
        <DeleteRuleButton {...this.props} />
      </WlContainer>
    );
  }
}

const mapStateToProps = state => ({
  ruleFieldOneOptions: state.RuleGroupData.ruleFieldOneOptions,
  ruleFieldTwoOptions: state.RuleGroupData.ruleFieldTwoOptions,
  ruleLogicFieldOptions: state.RuleGroupData.ruleLogicFieldOptions
});

export default connect(mapStateToProps)(RuleComponent);
