/**
 * BulkActionSubComponents : Has a list of subcomponents required for bulk
 * action component to reduce code complexity
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react"
import { TRASH_CATEGORY, ACTIVE_CATEGORY } from "./CategoryComponent"

/**
 * Returns list of options to show if the category is
 * active.
 */
const ActiveOptions = () => {
    
    return (
        <React.Fragment>
            <option value="duplicate">Duplicate</option>
            <option value="trash">Move to Trash</option>
        </React.Fragment>                          
    )
} 

/**
 * Returns list of options if the category is 
 * trash.
 */
const TrashOptions = () => {
    return (
        <React.Fragment>
            <option value="restore">Restore</option>
            <option value="trash">Delete Permanently</option>
        </React.Fragment>  
    )
}

/**
 * BulkActionOptions conditionally render the options
 * from the choosen category.
 * @param {Object} props 
 */
export const BulkActionOptions = ( props ) => {
    switch ( props.choosenCategory ) {
        case TRASH_CATEGORY:
            return <TrashOptions />
        case ACTIVE_CATEGORY:
            return <ActiveOptions />
    }
}
