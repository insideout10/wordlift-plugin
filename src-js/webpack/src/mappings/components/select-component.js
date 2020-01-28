/**
 * SelectComponent : component to render the selection box
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react";
import { connect } from "react-redux";

/**
 * Internal dependencies
 */
import { CHANGE_RULE_FIELD_VALUE_ACTION } from "../actions/actions";

class SelectComponent extends React.Component {
  constructor(props) {
    super(props);
    this.renderOptions = this.renderOptions.bind(this);
    this.renderOptionsForOptionGroup = this.renderOptionsForOptionGroup.bind(this);
  }

  renderOptions(options) {
    return options.map((item, index) => {
      return (
        <option key={index} value={item.value}>
          {item.label}
        </option>
      );
    });
  }
  renderOptionsForOptionGroup() {
    return this.props.options.map((item, index) => {
      return <optgroup label={item.group_name}>{this.renderOptions(item.group_options)}</optgroup>;
    });
  }

  renderOptionsConditionally() {
    if (this.props.inputDataIsOptionGroup) {
      return this.renderOptionsForOptionGroup();
    } else {
      return this.renderOptions(this.props.options);
    }
  }
  render() {
    return (
      <React.Fragment>
        <select value={this.props.value} className={this.props.className} onChange={this.props.onChange}>
          <option value="">Select one</option>
          {this.renderOptionsConditionally()}
        </select>
      </React.Fragment>
    );
  }
}

const mapStateToProps = function(state) {
  return {};
};
export default connect(mapStateToProps)(SelectComponent);
