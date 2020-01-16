/**
 * RuleGroupComponent : it displays the list of rules, let the user
 * add new rules
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react";
import PropTypes from "prop-types";

/**
 * Internal dependencies
 */
import RuleComponent from "./rule-component";
import { connect } from "react-redux";

class RuleGroupComponent extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    return (
      <div className="rule-group-container">
        {this.props.rules.map((ruleProps, ruleIndex) => {
          return (
            <RuleComponent ruleProps={ruleProps} ruleGroupIndex={this.props.ruleGroupIndex} ruleIndex={ruleIndex} />
          );
        })}
      </div>
    );
  }
}
RuleGroupComponent.propTypes = {
  rules: PropTypes.array
};
const mapStateToProps = function(state) {
  return {};
};
export default connect(mapStateToProps)(RuleGroupComponent);
