/**
 * PropertyHeaderRow : it shows the header row for the property.
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
import { PROPERTY_ITEM_SELECT_ALL_ACTION } from "../../../actions/actions";

class _PropertyHeaderRow extends React.Component {
  constructor(props) {
    super(props);
    this.selectAllPropertyHandler = this.selectAllPropertyHandler.bind(this);
  }
  selectAllPropertyHandler() {
    this.props.dispatch(PROPERTY_ITEM_SELECT_ALL_ACTION);
  }
  render() {
    return (
      <thead>
        <tr>
          <th className="wl-check-column">
            <input
              type="checkbox"
              onClick={() => {
                this.selectAllPropertyHandler();
              }}
              checked={this.props.propertyHeaderCheckboxClicked === true}
            />
          </th>
          <th style={{ width: "30%" }}>
            <b>Property</b>
          </th>
          <th>
            <b>Field</b>
          </th>
        </tr>
      </thead>
    );
  }
}

export const PropertyHeaderRow = connect()(_PropertyHeaderRow);
