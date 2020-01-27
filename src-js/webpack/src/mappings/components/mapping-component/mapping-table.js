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

/**
 * Internal dependencies.
 */
import {MappingHeaderRow, MappingNoActiveItemMessage} from "./mapping-list-subcomponents";
import MappingListItemComponent from "./mapping-list-item-component";
const {wl_edit_mapping_nonce} = global["wlMappingsConfig"];
export const MappingTable = ( props ) => {
    return (
        <table className="wp-list-table widefat striped wl-table">
            <thead>
            <MappingHeaderRow/>
            </thead>
            <tbody>
            <MappingNoActiveItemMessage {...props} />
            {props.mappingItems
                .filter(el => el.mappingStatus === props.chosenCategory)
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
};