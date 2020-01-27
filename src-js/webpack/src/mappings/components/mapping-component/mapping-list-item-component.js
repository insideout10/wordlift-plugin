/**
 * MappingListItemComponent : it displays the list of mapping items
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import { connect } from "react-redux";
import PropTypes from "prop-types";
import {ACTIVE_CATEGORY, TRASH_CATEGORY} from "../category-component";
import {MAPPING_ITEM_SELECTED_ACTION} from "../../actions/actions";
import {MappingListItemActiveCategoryOptions} from "./mapping-list-item-active-category-options";
import {MappingListItemTrashCategoryOptions} from "./mapping-list-item-trash-category-options";

class MappingListItemComponent extends React.Component {
  constructor(props) {
    super(props);
  }

  /**
   * Render the options based on the mapping list item category.
   * @param {String} category Category which the mapping items belong to
   */
  renderOptionsBasedOnItemCategory(category) {
    switch (category) {
      case ACTIVE_CATEGORY:
        return <MappingListItemActiveCategoryOptions {...this.props}/>;
      case TRASH_CATEGORY:
        return <MappingListItemTrashCategoryOptions {...this.props}/>;
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
                };
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
