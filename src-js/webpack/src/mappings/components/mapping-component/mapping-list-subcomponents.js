import React from "react";

/**
 * Contains subcomponents for the mapping list component.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

export const AddNewButton  = () => {
    return (
        <h1 className="wp-heading-inline wl-mappings-heading-text">
            Mappings &nbsp;&nbsp;
            <a href="?page=wl_edit_mapping" className="button wl-mappings-add-new">
                Add New
            </a>
        </h1>
    )
}

export const MappingTableCheckBox = ( props, selectAllMappingsHandler ) => {
    return (
        <th className="wl-check-column">
            <input
                type="checkbox"
                onClick={ selectAllMappingsHandler }
                checked={props.headerCheckBoxSelected === true}
            />
        </th>
    )
}