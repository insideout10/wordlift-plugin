/**
 * External dependencies.
 */
import React from "react";
import {connect} from 'react-redux';

/**
 * Internal dependencies.
 */
import {ACTIVE_CATEGORY} from "../category-component";
import {MAPPING_LIST_BULK_SELECT_ACTION, MAPPING_LIST_SORT_TITLE_CHANGED_ACTION} from "../../actions/actions";

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

const MappingTableCheckBox = connect( (state) => ({
    headerCheckBoxSelected: state.headerCheckBoxSelected
}))(_MappingTableCheckBox);

/**
 *
 * @param props Object passed from { @link MappingComponent }
 * @returns MappingTableTitleSort Instance
 */
class _MappingTableTitleSort  extends React.Component {
    constructor(props) {
        super(props);
        this.sortMappingItemsByTitle = this.sortMappingItemsByTitle.bind(this)
    }
    sortMappingItemsByTitle() {
        this.props.dispatch( MAPPING_LIST_SORT_TITLE_CHANGED_ACTION )
    }
    render() {
        return (
            <th>
                <a
                    className="row-title wl-mappings-link"
                    onClick={() => {
                        this.sortMappingItemsByTitle()
                    }}
                >
                    Title
                    <span className={"dashicons " + this.props.titleIcon}> </span>
                </a>
            </th>
        )
    }
}
const MappingTableTitleSort = connect( (state) => ({
    titleIcon: state.titleIcon
}))(_MappingTableTitleSort);

/**
 * Show the mapping header row in the mapping list table, reused in the table footer.
 * @param props Properties passed from {@link MappingComponent }
 * @returns MappingHeaderRow instance.
 */
export const MappingHeaderRow = ( ) => {
    return (
        <tr>
            <MappingTableCheckBox/>
            <MappingTableTitleSort/>
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