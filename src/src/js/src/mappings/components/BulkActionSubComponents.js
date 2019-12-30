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

export const BulkOptionValues = {
    TRASH: 'trash',
    DUPLICATE: 'duplicate',
    RESTORE: 'restore',
    DELETE_PERMANENTLY: 'delete_permanently'
}

/**
 * Returns list of options to show if the category is
 * active.
 */
const ActiveOptions = () => {
    
    return (
        <React.Fragment>
            <option value={ BulkOptionValues.DUPLICATE }>Duplicate</option>
            <option value={ BulkOptionValues.TRASH }>Move to Trash</option>
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
            <option value={ BulkOptionValues.RESTORE }>Restore</option>
            <option value={ BulkOptionValues.DELETE_PERMANENTLY }>Delete Permanently</option>
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
