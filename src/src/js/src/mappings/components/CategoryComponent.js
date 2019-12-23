/**
 * CategoryComponent : Displays the list of categories and  user can select 
 * select the category
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react"
import PropTypes from 'prop-types';

class CategoryComponent extends React.Component {

    constructor( props ) {
        super( props )
    }

    render() {
        return (
            <div>
                {
                    this.props.categories.map( ( category, index ) => {

                        return (
                            <span className="wl-mappings-link wl-category-title">
                                <a onClick={()=> { this.props.categorySelectHandler(category) }}>
                                   { category }
                                </a> 
                                ({
                                    // Count the category in the source
                                    this.props.source
                                    .filter( el=> el[this.props.categoryKeyName] === category )
                                    .length
                                }) | &nbsp;
                            </span>
                        )

                    })
                }
            </div>
        )
    }


}

CategoryComponent.propTypes = {
    // Category key : category key name of  object in source object list.
    categoryKeyName: PropTypes.string.isRequired,
    // List of categories needed to be shown for user
    categories: PropTypes.array.isRequired,
    // Source : Array of objects
    source: PropTypes.array.isRequired,
    // Category select handler
    categorySelectHandler: PropTypes.func.isRequired
}


export default CategoryComponent