/**
 * AddRuleButton : it handles adding the new rule from the rule group
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
import { ADD_NEW_RULE_ACTION } from "../../actions/actions";
import { WlColumn } from "../../blocks/wl-column";

class _AddRuleButton extends React.Component {
  constructor(props) {
    super(props);
    this.handleAddNewRule = this.handleAddNewRule.bind(this);
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
  render() {
    return (
      <WlColumn>
        <button
          className="button action wl-and-button"
          onClick={() => this.handleAddNewRule(this.props.ruleGroupIndex, this.props.ruleIndex)}
        >
          And
        </button>
      </WlColumn>
    );
  }
}

export const AddRuleButton = connect()(_AddRuleButton);
