/**
 * External dependencies.
 */
import React from "react";
import {connect} from 'react-redux';

/**
 * Internal dependencies.
 */
import {ACTIVE_CATEGORY} from "../category-component";
import {MAPPING_LIST_BULK_SELECT_ACTION} from "../../actions/actions";

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
class _MappingTableCheckBox extends React.Component {

    render() {
        return (
            <th className="wl-check-column">
                <input
                    type="checkbox"
                    onClick={ () => { this.props.dispatch( MAPPING_LIST_BULK_SELECT_ACTION) } }
                    checked={ this.props.headerCheckBoxSelected === true}
                />
            </th>
        )
    }
}

const MappingTableCheckBox = connect()(_MappingTableCheckBox);

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
                <span className={"dashicons " + props.titleIconClass} > </span>
            </a>
        </th>
    )
};

/**
 * Show the mapping header row in the mapping list table, reused in the table footer.
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