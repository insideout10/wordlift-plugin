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
import {
  ADD_NEW_RULE_ACTION,
  DELETE_RULE_ACTION,
  CHANGE_RULE_FIELD_VALUE_ACTION,
  NOTIFICATION_CHANGED_ACTION,
  MAPPING_TERMS_CHANGED_ACTION, EDIT_MAPPING_REQUEST_TERMS_ACTION
} from "../../actions/actions";
import {EDIT_MAPPING_REQUEST_TERMS} from "../../actions/action-types";

class RuleComponent extends React.Component {
  constructor(props) {
    super(props);

    this.handleAddNewRule = this.handleAddNewRule.bind(this);
    this.handleDeleteRule = this.handleDeleteRule.bind(this);
    this.handleSelectFieldChange = this.handleSelectFieldChange.bind(this);
    this.fetchTermsForSelectedTaxonomyFromAPI = this.fetchTermsForSelectedTaxonomyFromAPI.bind(this);

    // Load terms for the selected taxonomy.
    this.fetchTermsForSelectedTaxonomyFromAPI(this.props.ruleProps.ruleFieldOneValue);
  }
  /**
   * Adds a new rule after the current rule index
   *
   * @param {Number} ruleGroupIndex Index of the rule group which the rule belongs to
   * @param {Number} ruleIndex Index of the rule
   */
  handleAddNewRule(ruleGroupIndex, ruleIndex) {
    const action = ADD_NEW_RULE_ACTION;
    action.payload = {
      ruleGroupIndex: ruleGroupIndex,
      ruleIndex: ruleIndex
    };
    this.props.dispatch(action);
  }
  /**
   * Delete current rule at ruleIndex
   *
   * @param {Number} ruleGroupIndex Index of the rule group which the rule belongs to
   * @param {Number} ruleIndex Index of the rule
   */
  handleDeleteRule(ruleGroupIndex, ruleIndex) {
    const action = DELETE_RULE_ACTION;
    action.payload = {
      ruleGroupIndex: ruleGroupIndex,
      ruleIndex: ruleIndex
    };
    this.props.dispatch(action);
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
      this.fetchTermsForSelectedTaxonomyFromAPI(event.target.value);
    }
    this.props.dispatch(action);
  }
  /**
   * Fetches the terms for the selected taxonomy to the ui.
   * @param selectedTaxonomy The taxonomy selected by the user.
   */
  fetchTermsForSelectedTaxonomyFromAPI(selectedTaxonomy) {
    // Check if the terms are fetched for the taxonomy.
    const taxonomies = this.props.ruleFieldOneOptions.filter(e => e.value === selectedTaxonomy);
    if (1 === taxonomies.length) {
      const selectedTaxonomyOption = taxonomies[0];
      if (!selectedTaxonomyOption.isTermsFetchedForTaxonomy) {
        // if the terms are not fetched from api, then send a network request.
        EDIT_MAPPING_REQUEST_TERMS_ACTION.payload = {
          taxonomy: selectedTaxonomy
        };
        this.props.dispatch( EDIT_MAPPING_REQUEST_TERMS_ACTION )
      }
    }
  }



  render() {
    return (
      <div className="wl-container wl-rule-container">
        <div className="wl-col">
          <SelectComponent
            options={this.props.ruleFieldOneOptions}
            value={this.props.ruleProps.ruleFieldOneValue}
            onChange={e => {
              this.handleSelectFieldChange(e, "ruleFieldOneValue");
            }}
            className="wl-field-one-select wl-form-select"
          />
        </div>
        <div className="wl-col">
          <SelectComponent
            options={this.props.ruleLogicFieldOptions}
            value={this.props.ruleProps.ruleLogicFieldValue}
            onChange={e => {
              this.handleSelectFieldChange(e, "ruleLogicFieldValue");
            }}
            className="wl-field-logic wl-form-select"
          />
        </div>
        <div className="wl-col">
          <SelectComponent
            options={this.props.ruleFieldTwoOptions.filter(
              el => el.taxonomy === this.props.ruleProps.ruleFieldOneValue
            )}
            value={this.props.ruleProps.ruleFieldTwoValue}
            onChange={e => {
              this.handleSelectFieldChange(e, "ruleFieldTwoValue");
            }}
            className="wl-field-two-select wl-form-select"
          />
        </div>
        <div className="wl-col">
          <button
            className="button action wl-and-button"
            onClick={() => this.handleAddNewRule(this.props.ruleGroupIndex, this.props.ruleIndex)}
          >
            And
          </button>
        </div>
        {(0 !== this.props.ruleGroupIndex || 0 !== this.props.ruleIndex) && (
          <div className="wl-col">
            <button
              className="button action wl-remove-button dashicons dashicons-trash"
              onClick={() => this.handleDeleteRule(this.props.ruleGroupIndex, this.props.ruleIndex)}
            />
          </div>
        )}
      </div>
    );
  }
}

const mapStateToProps = state => ({
  ruleFieldOneOptions: state.RuleGroupData.ruleFieldOneOptions,
  ruleFieldTwoOptions: state.RuleGroupData.ruleFieldTwoOptions,
  ruleLogicFieldOptions: state.RuleGroupData.ruleLogicFieldOptions
});

export default connect(mapStateToProps)(RuleComponent);
