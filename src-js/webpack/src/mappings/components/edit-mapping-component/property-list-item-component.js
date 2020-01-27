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
import { PROPERTY_ITEM_CATEGORY_CHANGED_ACTION, PROPERTY_ITEM_CRUD_OPERATION_ACTION } from "../../actions/actions";
import { TRASH_CATEGORY, ACTIVE_CATEGORY } from "../category-component";

/** Constants to be supplied via actions, and also compared in
 * the property reducers for making a CRUD Action on the property
 * list.
 */
export const DUPLICATE_PROPERTY = "duplicate_property";
export const DELETE_PROPERTY_PERMANENT = "delete_property_permanent";

const RowActionItem = ({ className, title, onClickHandler, args }) => {
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

    this.returnOptionsForActiveCategory = this.returnOptionsForActiveCategory.bind(this);
    this.renderOptionsBasedOnItemCategory = this.renderOptionsBasedOnItemCategory.bind(this);
    this.changeCategoryPropertyItem = this.changeCategoryPropertyItem.bind(this);
    this.makeCrudOperationOnPropertyId = this.makeCrudOperationOnPropertyId.bind(this);
  }
  /**
   * Return the options for the trash category.
   */
  returnOptionsForTrashCategory() {
    return (
      <React.Fragment>
        <RowActionItem
          className="edit wl-mappings-link"
          onClickHandler={this.changeCategoryPropertyItem}
          title="Restore"
          args={[this.props.propData.property_id, ACTIVE_CATEGORY]}
        />
        <RowActionItem
          className="trash wl-mappings-link"
          onClickHandler={this.makeCrudOperationOnPropertyId}
          title="Delete Permanently"
          args={[this.props.propData.property_id, DELETE_PROPERTY_PERMANENT]}
        />
      </React.Fragment>
    );
  }
  /**
   * Return the template for the active category.
   */
  returnOptionsForActiveCategory() {
    return (
      <React.Fragment>
        <RowActionItem
          className="edit wl-mappings-link"
          onClickHandler={this.props.switchState}
          title="Edit"
          args={[this.props.propData.property_id]}
        />
        <RowActionItem
          className="wl-mappings-link"
          onClickHandler={this.makeCrudOperationOnPropertyId}
          title="Duplicate"
          args={[this.props.propData.property_id, DUPLICATE_PROPERTY]}
        />
        <RowActionItem
          className="wl-mappings-link trash"
          onClickHandler={this.changeCategoryPropertyItem}
          title="Trash"
          args={[this.props.propData.property_id, TRASH_CATEGORY]}
        />
      </React.Fragment>
    );
  }

  /**
   * Render the options based on the mapping list item category.
   * @param {String} category Category which the mapping items belong to
   */
  renderOptionsBasedOnItemCategory(category) {
    switch (category) {
      case ACTIVE_CATEGORY:
        return this.returnOptionsForActiveCategory();
      case TRASH_CATEGORY:
        return this.returnOptionsForTrashCategory();
    }
  }
  changeCategoryPropertyItem(propertyId, category) {
    const action = PROPERTY_ITEM_CATEGORY_CHANGED_ACTION;
    action.payload = {
      propertyId: propertyId,
      propertyCategory: category
    };
    this.props.dispatch(action);
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
        <div className="wl-col">
          <a className="row-title wl-property-list-item-title">{this.props.propData.propertyHelpText}</a>
          <div className="row-actions">{this.renderOptionsBasedOnItemCategory(this.props.choosenCategory)}</div>
        </div>
      </div>
    );
  }
}

PropertyListItemComponent.propTypes = {
  propertyText: PropTypes.string
};

export default connect()(PropertyListItemComponent);
