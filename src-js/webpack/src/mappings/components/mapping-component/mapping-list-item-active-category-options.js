/**
 * MappingListItemActiveCategoryOptions : it returns the option available for the active category.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import { connect } from "react-redux";

/**
 * Internal dependencies.
 */
import { TRASH_CATEGORY } from "../category-component";
import {
  MAPPINGS_REQUEST_CLONE_MAPPINGS_ACTION,
  MAPPINGS_REQUEST_DELETE_OR_UPDATE_ACTION
} from "../../actions/actions";

class _MappingListItemActiveCategoryOptions extends React.Component {
  constructor(props) {
    super(props);
    this.updateMappingItem = this.updateMappingItem.bind(this);
    this.duplicateMappingItems = this.duplicateMappingItems.bind(this);
  }
  // Updates or deletes the mapping items based on the request
  updateMappingItem(mappingItem, type = "PUT") {
    MAPPINGS_REQUEST_DELETE_OR_UPDATE_ACTION.payload = {
      type: type,
      mappingItems: [mappingItem]
    };
    this.props.dispatch(MAPPINGS_REQUEST_DELETE_OR_UPDATE_ACTION);
  }
  /**
   * @param {Array|Object} mappingItems accepts a single
   * mapping item object or multiple mapping items, clone them by posting
   * to the api endpoint and then refresh the current list.
   */
  duplicateMappingItems(mappingItems) {
    // If single item is given, construct it to array
    mappingItems = Array.isArray(mappingItems) ? mappingItems : [mappingItems];
    MAPPINGS_REQUEST_CLONE_MAPPINGS_ACTION.payload = {
      mappingItems: mappingItems
    };
    this.props.dispatch(MAPPINGS_REQUEST_CLONE_MAPPINGS_ACTION);
  }

  constructEditMappingLink() {
    return (
      "?page=wl_edit_mapping" +
      "&_wl_edit_mapping_nonce=" +
      this.props.nonce +
      "&wl_edit_mapping_id=" +
      this.props.mappingData.mappingId
    );
  }

  render() {
    return (
      <React.Fragment>
        <span className="edit">
          <a href={this.constructEditMappingLink()}>Edit</a>|
        </span>
        <span className="wl-mappings-link">
          <a
            title="Duplicate this item"
            onClick={() => {
              this.duplicateMappingItems(this.props.mappingData);
            }}
          >
            Duplicate
          </a>{" "}
          |
        </span>
        <span className="trash wl-mappings-link">
          <a
            onClick={() => {
              const mappingData = Object.assign({}, this.props.mappingData);
              mappingData.mappingStatus = TRASH_CATEGORY;
              this.updateMappingItem(this.props.mappingData);
            }}
          >
            Trash
          </a>
        </span>
      </React.Fragment>
    );
  }
}

export const MappingListItemActiveCategoryOptions = connect()(_MappingListItemActiveCategoryOptions);
