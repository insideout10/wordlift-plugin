/**
 * PropertyItemTrashOptions : it displays the options for a property item with trash category.
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
import {ACTIVE_CATEGORY} from "../../category-component";
import {DELETE_PROPERTY_PERMANENT, RowActionItem} from "./property-list-item-component";

export const PropertyItemTrashOptions = ({makeCrudOperationOnPropertyId, propData, changeCategoryPropertyItem }) => {
    return (
        <React.Fragment>
            <RowActionItem
                className="edit wl-mappings-link"
                onClickHandler={changeCategoryPropertyItem}
                title="Restore"
                args={[propData.property_id, ACTIVE_CATEGORY]}
            />
            <RowActionItem
                className="trash wl-mappings-link"
                onClickHandler={makeCrudOperationOnPropertyId}
                title="Delete Permanently"
                args={[propData.property_id, DELETE_PROPERTY_PERMANENT]}
            />
        </React.Fragment>
    );
};
