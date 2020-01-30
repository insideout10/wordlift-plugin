/**
 * MappingHeaderCheckbox : it displays the mapping header table checkbox to select all mapping items.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react"
import { connect } from "react-redux";

/**
 * Internal dependencies.
 */
import { MAPPING_LIST_BULK_SELECT_ACTION } from "../../actions/actions";

/**
 * MappingHeaderCheckbox : Provides a checkbox to user to select all the mapping items.
 */
class _MappingHeaderCheckbox extends React.Component {
  render() {
    return (
      <th className="wl-check-column">
        <input
            className={"wl-table__checkbox"}
          type="checkbox"
          onClick={() => {
            this.props.dispatch(MAPPING_LIST_BULK_SELECT_ACTION);
          }}
          checked={this.props.headerCheckBoxSelected === true}
        />
      </th>
    );
  }
}

export const MappingHeaderCheckbox = connect(state => ({
  headerCheckBoxSelected: state.headerCheckBoxSelected
}))(_MappingHeaderCheckbox);
