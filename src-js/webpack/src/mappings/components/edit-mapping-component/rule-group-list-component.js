/**
 * RuleGroupListComponent : it displays the list of rule groups, let the user
 * add/remove new rule groups
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
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
import RuleGroupComponent from "./rule-group-component";
import { ADD_NEW_RULE_GROUP_ACTION } from "../../actions/actions";

class RuleGroupListComponent extends React.Component {
  constructor(props) {
    super(props);

    this.addNewRuleGroupHandler = this.addNewRuleGroupHandler.bind(this);
  }

  addNewRuleGroupHandler() {
    this.props.dispatch(ADD_NEW_RULE_GROUP_ACTION);
  }

  render() {
    return (
      <React.Fragment>
        {0 === this.props.ruleGroupList.length && (
          <div className="wl-col">No rule groups present, click on add new</div>
        )}
        {this.props.ruleGroupList.map((item, index) => {
          return (
            <React.Fragment key={index}>
              <RuleGroupComponent rules={item.rules} ruleGroupIndex={index} />
              {// dont show extra `or` text if there
              // is no rule group below
              index !== this.props.ruleGroupList.length - 1 && (
                <div className="wl-container">
                  <div className="wl-col">
                    <b>Or</b>
                  </div>
                </div>
              )}
            </React.Fragment>
          );
        })}

        <div className="wl-container">
          <div className="wl-col">
            <button
              className="button action wl-add-rule-group"
              onClick={() => {
                this.addNewRuleGroupHandler();
              }}
            >
              Add Rule Group
            </button>
          </div>
        </div>
      </React.Fragment>
    );
  }
}

RuleGroupListComponent.propTypes = {
  ruleGroupList: PropTypes.array
};

const mapStateToProps = function(state) {
  return {
    ruleGroupList: state.RuleGroupData.ruleGroupList
  };
};

export default connect(mapStateToProps)(RuleGroupListComponent);
