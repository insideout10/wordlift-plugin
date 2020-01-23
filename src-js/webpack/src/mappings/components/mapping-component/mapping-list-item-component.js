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
import { ACTIVE_CATEGORY } from "../category-component";

class MappingListItemComponent extends React.Component {
  constructor(props) {
    super(props);

    this.constructEditMappingLink = this.constructEditMappingLink.bind(this);
  }
  constructEditMappingLink() {
    return (
      "?page=wl_edit_mapping" +
      "&_wl_edit_mapping_nonce=" +
      this.props.nonce +
      "&wl_edit_mapping_id=" +
      this.props.mappingData.mapping_id
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
              this.props.switchCategoryHandler(this.props.mappingData, ACTIVE_CATEGORY);
            }}
          >
            Restore
          </a>
          |
        </span>
        <span className="trash wl-mappings-link">
          <a
            onClick={() => {
              this.props.deleteMappingItemHandler([this.props.mappingData], "DELETE");
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
              this.props.duplicateMappingItemHandler(this.props.mappingData);
            }}
          >
            Duplicate
          </a>{" "}
          |
        </span>
        <span className="trash wl-mappings-link">
          <a
            onClick={() => {
              this.props.switchCategoryHandler(this.props.mappingData, "trash");
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
            defaultChecked={this.props.mappingData.isSelected}
            onClick={() => {
              this.props.selectMappingItemHandler(this.props.mappingData);
            }}
          />
        </td>
        <td>
          <a className="row-title wl-mappings-list-item-title">{this.props.mappingData.mapping_title}</a>
          <div className="row-actions">{this.renderOptionsBasedOnItemCategory(this.props.mappingData.mapping_status)}</div>
        </td>
      </tr>
    );
  }
}

MappingListItemComponent.propTypes = {
  nonce: PropTypes.string,
  mappingData: PropTypes.object,
  mappingIndex: PropTypes.number
};
export default connect()(MappingListItemComponent);
