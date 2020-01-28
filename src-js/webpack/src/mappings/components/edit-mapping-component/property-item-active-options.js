/**
 * PropertyItemActiveOptions : it displays the options for a property item with active category.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import React from "react";
/**
 * Internal dependencies.
 */
import {TRASH_CATEGORY} from "../category-component";
import {DUPLICATE_PROPERTY, RowActionItem} from "./property-list-item-component";

export const PropertyItemActiveOptions = ({ switchState,makeCrudOperationOnPropertyId, propData, changeCategoryPropertyItem }) => {
    return (
        <React.Fragment>
            <RowActionItem
                className="edit wl-mappings-link"
                onClickHandler={switchState}
                title="Edit"
                args={[propData.property_id]}
            />
            <RowActionItem
                className="wl-mappings-link"
                onClickHandler={makeCrudOperationOnPropertyId}
                title="Duplicate"
                args={[propData.property_id, DUPLICATE_PROPERTY]}
            />
            <RowActionItem
                className="wl-mappings-link trash"
                onClickHandler={changeCategoryPropertyItem}
                title="Trash"
                args={[propData.property_id, TRASH_CATEGORY]}
            />
        </React.Fragment>
    );
};
