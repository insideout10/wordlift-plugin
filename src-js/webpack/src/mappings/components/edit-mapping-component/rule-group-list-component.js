/**
 * RuleGroupListComponent : it displays the list of rule groups, let the user
 * add/remove new rule groups
 *
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
import RuleGroupComponent from "./rule-group-component";
import { ADD_NEW_RULE_GROUP_ACTION } from "../../actions/actions";
import {AddRuleGroupButton} from "./add-rule-group-button";

class RuleGroupListComponent extends React.Component {
  constructor(props) {
    super(props);
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
        <AddRuleGroupButton/>
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
