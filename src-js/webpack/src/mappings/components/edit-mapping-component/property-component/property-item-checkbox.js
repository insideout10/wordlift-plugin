/**
 * PropertyItemCheckbox : it shows the checkbox for the each property item.
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
import { PROPERTY_ITEM_SELECTED_ACTION } from "../../../actions/actions";

class _PropertyItemCheckbox extends React.Component {
  constructor(props) {
    super(props);
    this.propertySelectedHandler = this.propertySelectedHandler.bind(this);
  }

  propertySelectedHandler(propertyId) {
    const action = PROPERTY_ITEM_SELECTED_ACTION;
    action.payload = {
      propertyId: propertyId
    };
    this.props.dispatch(action);
  }

  render() {
    return (
      <td className="wl-check-column">
        <input
          type="checkbox"
          checked={this.props.property.isSelectedByUser}
          onClick={() => {
            this.propertySelectedHandler(this.props.property.property_id);
          }}
        />
      </td>
    );
  }
}

export const PropertyItemCheckbox = connect()(_PropertyItemCheckbox);
