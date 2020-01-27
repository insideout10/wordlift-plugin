/**
 * MappingListItemComponent : it displays the list of mapping items
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react";
import { connect } from "react-redux";
import PropTypes from "prop-types";
import {ACTIVE_CATEGORY, TRASH_CATEGORY} from "../category-component";
import {
    MAPPING_ITEM_CATEGORY_CHANGED_ACTION,
    MAPPING_ITEM_SELECTED_ACTION, MAPPINGS_REQUEST_CLONE_MAPPINGS_ACTION,
    MAPPINGS_REQUEST_DELETE_OR_UPDATE_ACTION
} from "../../actions/actions";

class MappingListItemComponent extends React.Component {
  constructor(props) {
    super(props);
    this.constructEditMappingLink = this.constructEditMappingLink.bind(this);
    this.updateMappingItem = this.updateMappingItem.bind(this);
    this.duplicateMappingItems = this.duplicateMappingItems.bind(this);
  }
    // Updates or deletes the mapping items based on the request
    updateMappingItem(mappingItem, type = "PUT") {
        MAPPINGS_REQUEST_DELETE_OR_UPDATE_ACTION.payload = {
            type: type,
            mappingItems: [mappingItem]
        };
        this.props.dispatch( MAPPINGS_REQUEST_DELETE_OR_UPDATE_ACTION )
    }

    /**
     *
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
        this.props.dispatch(MAPPINGS_REQUEST_CLONE_MAPPINGS_ACTION)
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
  /**
   * Return the options for the trash category.
   */
  returnOptionsForTrashCategory() {
    return (
      <React.Fragment>
        <span className="edit wl-mappings-link">
          <a
            onClick={() => {
                const mappingData = this.props.mappingData;
                mappingData.mappingStatus = ACTIVE_CATEGORY;
                this.updateMappingItem(this.props.mappingData);
            }}
          >
            Restore
          </a>
          |
        </span>
        <span className="trash wl-mappings-link">
          <a
            onClick={() => {
              this.updateMappingItem([this.props.mappingData], "DELETE");
            }}
          >
            Delete Permanently
          </a>{" "}
          |
        </span>
      </React.Fragment>
    );
  }
  /**
   * Return the template for the active category.
   */
  returnOptionsForActiveCategory() {
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
                const mappingData = this.props.mappingData;
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
  /**
   * Render the options based on the mapping list item category.
   * @param {String} category Category which the mapping items belong to
   */
  renderOptionsBasedOnItemCategory(category) {
    switch (category) {
      case "active":
        return this.returnOptionsForActiveCategory();
      case "trash":
        return this.returnOptionsForTrashCategory();
    }
  }
  render() {
    return (
      <tr>
        <td className="wl-check-column">
          <input
            type="checkbox"
            checked={this.props.mappingData.isSelected}
            onClick={() => {
                MAPPING_ITEM_SELECTED_ACTION.payload = {
                    mappingId: this.props.mappingData.mappingId
                }
                this.props.dispatch( MAPPING_ITEM_SELECTED_ACTION )
            }}
          />
        </td>
        <td>
          <a className="row-title wl-mappings-list-item-title">{this.props.mappingData.mappingTitle}</a>
          <div className="row-actions">{this.renderOptionsBasedOnItemCategory(this.props.mappingData.mappingStatus)}</div>
        </td>
      </tr>
    );
  }
}

MappingListItemComponent.propTypes = {
  nonce: PropTypes.string,
  mappingData: PropTypes.object
};
export default connect()(MappingListItemComponent);
