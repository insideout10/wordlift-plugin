/**
 * MappingTable : it displays the full mapping table.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */


/**
 * External dependencies.
 */
import React from "react";
import { connect } from "react-redux"

/**
 * Internal dependencies.
 */
import {MappingHeaderRow, MappingNoActiveItemMessage} from "./mapping-list-subcomponents";
import MappingListItemComponent from "./mapping-list-item-component";
const {wl_edit_mapping_nonce} = global["wlMappingsConfig"];


class _MappingTable extends React.Component{
    render() {
        return (
            <table className="wp-list-table widefat striped wl-table">
                <thead>
                <MappingHeaderRow/>
                </thead>
                <tbody>
                <MappingNoActiveItemMessage {...this.props} />
                {this.props.mappingItems
                    .filter(el => el.mappingStatus === this.props.chosenCategory)
                    .map((item, index) => {
                        return (
                            <MappingListItemComponent
                                key={index}
                                nonce={wl_edit_mapping_nonce}
                                mappingData={item}
                            />
                        );
                    })}
                </tbody>
                <tfoot>
                <MappingHeaderRow/>
                </tfoot>
            </table>
        )
    }

}
export const MappingTable = connect((state)=>({
    mappingItems: state.mappingItems,
    chosenCategory: state.chosenCategory,
}))(_MappingTable);