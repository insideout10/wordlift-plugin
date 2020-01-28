/**
 * BulkActionComponent : Displays the list of bulk actions
 * based on the category
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react";
import { BulkActionOptions } from "./bulk-action-sub-components";

/** Bulk action apply button */
const BulkActionApplyButton = ({ bulkActionSubmitHandler }) => {
  return (
    <div className="wl-col">
      <button
        className="button action"
        onClick={() => {
          bulkActionSubmitHandler();
        }}
      >
        Apply
      </button>
    </div>
  );
};

/** Bulk action options wrapper */
const BulkActionOptionsWrapper = ({ chosenCategory, bulkActionOptionChangedHandler }) => {
  return (
    <div className="wl-col">
      <select
        className="form-control"
        onChange={event => {
          bulkActionOptionChangedHandler(event);
        }}
      >
        <option value="-1">Bulk Actions</option>
        <BulkActionOptions chosenCategory={chosenCategory} />
      </select>
    </div>
  );
};

class BulkActionComponent extends React.Component {
  render() {
    return (
      <React.Fragment>
        <BulkActionOptionsWrapper
          bulkActionOptionChangedHandler={this.props.bulkActionOptionChangedHandler}
          chosenCategory={this.props.chosenCategory}
        />
        <BulkActionApplyButton bulkActionSubmitHandler={this.props.bulkActionSubmitHandler} />
      </React.Fragment>
    );
  }
}

export default BulkActionComponent;
