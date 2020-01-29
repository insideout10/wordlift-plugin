/**
 * PropertyListItemComponent : used to display a single
 * property item with the title property help text
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
/**
 * Internal dependencies.
 */
import { PROPERTY_ITEM_CATEGORY_CHANGED_ACTION, PROPERTY_ITEM_CRUD_OPERATION_ACTION } from "../../../actions/actions";
import { TRASH_CATEGORY, ACTIVE_CATEGORY } from "../../category-component";
import { PropertyItemActiveOptions } from "./property-item-active-options";
import { PropertyItemTrashOptions } from "./property-item-trash-options";
import { WlColumn } from "../../../blocks/wl-column";

/** Constants to be supplied via actions, and also compared in
 * the property reducers for making a CRUD Action on the property
 * list.
 */
export const DUPLICATE_PROPERTY = "duplicate_property";
export const DELETE_PROPERTY_PERMANENT = "delete_property_permanent";

export const RowActionItem = ({ className, title, onClickHandler, args }) => {
  return (
    <span className={className}>
      <a
        onClick={() => {
          onClickHandler(...args);
        }}
      >
        {title}
      </a>
      |
    </span>
  );
};

class PropertyListItemComponent extends React.Component {
  constructor(props) {
    super(props);
    this.changeCategoryPropertyItem = this.changeCategoryPropertyItem.bind(this);
    this.makeCrudOperationOnPropertyId = this.makeCrudOperationOnPropertyId.bind(this);
  }
  /**
   * Render the options based on the mapping list item category.
   * @param {String} category Category which the mapping items belong to
   */
  renderOptionsBasedOnItemCategory(category) {
    switch (category) {
      case ACTIVE_CATEGORY:
        return (
          <PropertyItemActiveOptions
            {...this.props}
            changeCategoryPropertyItem={this.changeCategoryPropertyItem}
            makeCrudOperationOnPropertyId={this.makeCrudOperationOnPropertyId}
          />
        );
      case TRASH_CATEGORY:
        return (
          <PropertyItemTrashOptions
            {...this.props}
            changeCategoryPropertyItem={this.changeCategoryPropertyItem}
            makeCrudOperationOnPropertyId={this.makeCrudOperationOnPropertyId}
          />
        );
    }
  }
  changeCategoryPropertyItem(propertyId, category) {
    PROPERTY_ITEM_CATEGORY_CHANGED_ACTION.payload = {
      propertyId: propertyId,
      propertyCategory: category
    };
    this.props.dispatch(PROPERTY_ITEM_CATEGORY_CHANGED_ACTION);
  }

  makeCrudOperationOnPropertyId(propertyId, operationName) {
    const action = PROPERTY_ITEM_CRUD_OPERATION_ACTION;
    action.payload = {
      propertyId: propertyId,
      operationName: operationName
    };
    this.props.dispatch(action);
  }

  render() {
    return (
      <div className="wl-property-list-item wl-container">
        <WlColumn>
          <a className="row-title wl-property-list-item-title">{this.props.propData.propertyHelpText}</a>
          <div className="row-actions">{this.renderOptionsBasedOnItemCategory(this.props.chosenCategory)}</div>
        </WlColumn>
      </div>
    );
  }
}

PropertyListItemComponent.propTypes = {
  propertyText: PropTypes.string
};

export default connect()(PropertyListItemComponent);
