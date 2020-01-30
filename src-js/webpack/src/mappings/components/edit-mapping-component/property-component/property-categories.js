/**
 * PropertyCategories : it shows the property categories above the property list
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
import { PROPERTY_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION } from "../../../actions/actions";
import CategoryComponent from "../../category-component";

class _PropertyCategories extends React.Component {
  constructor(props) {
    super(props);
    this.categorySelectHandler = this.categorySelectHandler.bind(this);
  }

  categorySelectHandler(category) {
    const action = PROPERTY_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION;
    action.payload = {
      chosenCategory: category
    };
    this.props.dispatch(action);
  }
  render() {
    return (
      <CategoryComponent
        source={this.props.propertyList}
        categoryKeyName="propertyStatus"
        categories={["active", "trash"]}
        categorySelectHandler={this.categorySelectHandler}
        chosenCategory={this.props.chosenCategory}
      />
    );
  }
}

export const PropertyCategories = connect()(_PropertyCategories);
