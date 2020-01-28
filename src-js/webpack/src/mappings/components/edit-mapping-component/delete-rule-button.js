/**
 * DeleteRuleButton : it handles the deletion of rule from the rule group.
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
import { DELETE_RULE_ACTION } from "../../actions/actions";

class _DeleteRuleButton extends React.Component {
  constructor(props) {
    super(props);
    this.handleDeleteRule = this.handleDeleteRule.bind(this);
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
  render() {
    return (
      <React.Fragment>
        {(0 !== this.props.ruleGroupIndex || 0 !== this.props.ruleIndex) && (
          <div className="wl-col">
            <button
              className="button action wl-remove-button dashicons dashicons-trash"
              onClick={() => this.handleDeleteRule(this.props.ruleGroupIndex, this.props.ruleIndex)}
            />
          </div>
        )}
      </React.Fragment>
    );
  }
}

export const DeleteRuleButton = connect()(_DeleteRuleButton);
