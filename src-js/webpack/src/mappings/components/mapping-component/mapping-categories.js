/**
 * MappingCategories : it displays the categories for the mapping items.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */


/**
 * External dependencies
 */
import React from "react";
import {connect} from "react-redux";

/**
 * Internal dependencies.
 */
import {MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION} from "../../actions/actions";
import CategoryComponent from "../category-component";

class _MappingCategories extends React.Component {
    constructor(props) {
        super(props);
        this.selectCategory = this.selectCategory.bind(this)
    }
    /**
     * When the category is selected in the categoryComponent this method
     * is fired.
     * @param {String} category The category choosen by the user
     * @return void
     */
    selectCategory( category ) {
        const action = MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION;
        action.payload = {
            categoryName: category
        };
        this.props.dispatch(action);
    }
    render()
    {
        return (
            <CategoryComponent
                source={this.props.mappingItems}
                categoryKeyName="mappingStatus"
                categories={["active", "trash"]}
                categorySelectHandler={this.selectCategory}
                choosenCategory={this.props.chosenCategory}
            />
        )
    }
}

export const MappingCategories = connect((state)=>({
    mappingItems: state.mappingItems,
    chosenCategory: state.chosenCategory,
}))(_MappingCategories);