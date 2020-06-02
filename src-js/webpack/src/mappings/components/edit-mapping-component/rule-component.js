/**
 * RuleComponent : Displays a single rule
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */
/**
 * External dependencies
 */
import React from "react";
import {connect} from "react-redux";
/**
 * Internal dependencies
 */
import SelectComponent from "../select-component";
import {CHANGE_RULE_FIELD_VALUE_ACTION, EDIT_MAPPING_REQUEST_TERMS_ACTION} from "../../actions/actions";
import {AddRuleButton} from "./add-rule-button";
import {DeleteRuleButton} from "./delete-rule-button";
import {WlColumn} from "../../blocks/wl-column";
import {WlContainer} from "../../blocks/wl-container";
import {EDIT_MAPPING_REQUEST_TERMS} from "../../actions/action-types";
import {getTermsForTaxonomy} from "../../store/selectors";
import editMappingStore from "../../store/edit-mapping-store";

class RuleComponent extends React.Component {
  constructor(props) {
    super(props);
    this.handleSelectFieldChange = this.handleSelectFieldChange.bind(this);
    this.fetchTermsForSelectedTaxonomyFromAPI = this.fetchTermsForSelectedTaxonomyFromAPI.bind(this);
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
    const terms = getTermsForTaxonomy(editMappingStore.getState(), selectedTaxonomy);

    if (0 === terms.length && selectedTaxonomy !== "post_type") {
      // if the terms are not fetched from api, then send a network request.
      this.props.dispatch({
        type: EDIT_MAPPING_REQUEST_TERMS,
        payload: {taxonomy: selectedTaxonomy}
      });
    }
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
                    el => el.taxonomy === this.props.ruleProps.ruleFieldOneValue
                )}
                value={this.props.ruleProps.ruleFieldTwoValue}
                onChange={e => {
                  this.handleSelectFieldChange(e, "ruleFieldTwoValue");
                }}
                className="wl-field-two-select wl-form-select"
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
