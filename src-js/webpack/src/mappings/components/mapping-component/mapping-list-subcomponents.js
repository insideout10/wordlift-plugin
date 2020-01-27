/**
 * External dependencies.
 */
import React from "react";
import {connect} from 'react-redux';

/**
 * Internal dependencies.
 */
import {ACTIVE_CATEGORY} from "../category-component";
import {MappingHeaderTitle} from "./mapping-header-title";
import {MappingHeaderCheckbox} from "./mapping-header-checkbox";

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
 * Show the mapping header row in the mapping list table, reused in the table footer.
 * @param props Properties passed from {@link MappingComponent }
 * @returns MappingHeaderRow instance.
 */
export const MappingHeaderRow = ( ) => {
    return (
        <tr>
            <MappingHeaderCheckbox/>
            <MappingHeaderTitle/>
        </tr>
    )
}

export const MappingNoActiveItemMessage = ( props ) => {
    return (
        0 === props.mappingItems.filter(el => el.mappingStatus === ACTIVE_CATEGORY).length &&
        props.chosenCategory === ACTIVE_CATEGORY && (
            <tr>
                <td colSpan={3}>
                    <div className="wl-container text-center">
                        No Mapping items found, click on
                        <b>&nbsp; Add New </b>
                    </div>
                </td>
            </tr>
        )
    )
};