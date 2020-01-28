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
import {AddRuleGroupButton} from "./add-rule-group-button";
import {RuleGroupText} from "./rule-group-text";

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
              <RuleGroupText {...this.props} index={index} />
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
