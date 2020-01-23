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
};

/**
 * MappingTableCheckBox : Provides a checkbox to user to select all the mapping items.
 * @param props Properties required for the MappingTableCheckBox
 * @returns MappingTableCheckBox instance.
 */
export const MappingTableCheckBox = ( props ) => {
    return (
        <th className="wl-check-column">
            <input
                type="checkbox"
                onClick={ () => { props.selectAllMappingsHandler() } }
                checked={ props.headerCheckBoxSelected === true}
            />
        </th>
    )
};

/**
 *
 * @param props Object passed from { @link MappingComponent }
 * @returns MappingTableTitleSort Instance
 */
export const MappingTableTitleSort = ( props ) => {
    return (
        <th>
            <a
                className="row-title wl-mappings-link"
                onClick={() => {
                    props.sortMappingItemsByTitleHandler()
                }}
            >
                Title
                <span className={"dashicons " + props.titleIconClass} />
            </a>
        </th>
    )
};

/**
 *
 * @param props Properties passed from {@link MappingComponent }
 * @returns MappingHeaderRow instance.
 */
export const MappingHeaderRow = ( props ) => {
    return (
        <tr>
            <MappingTableCheckBox
                { ...props }
            />
            <MappingTableTitleSort
                { ...props }
            />
        </tr>
    )
}