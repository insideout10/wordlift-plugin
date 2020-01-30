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
import { connect } from "react-redux";

/**
 * Internal dependencies.
 */
import { MappingNoActiveItemMessage } from "./mapping-no-active-item-message";
import MappingListItemComponent from "./mapping-list-item-component";
import { MappingHeaderRow } from "./mapping-header-row";
import {WlTable} from "../../blocks/wl-table";
const { wl_edit_mapping_nonce } = global["wlMappingsConfig"];

class _MappingTable extends React.Component {
  render() {
    return (
<WlTable>
        <thead>
          <MappingHeaderRow />
        </thead>
        <tbody>
          <MappingNoActiveItemMessage {...this.props} />
          {this.props.mappingItems.filter(el => el.mappingStatus === this.props.chosenCategory).map((item, index) => {
            return <MappingListItemComponent key={index} nonce={wl_edit_mapping_nonce} mappingData={item} />;
          })}
        </tbody>
        <tfoot>
          <MappingHeaderRow />
        </tfoot>
</WlTable>
    );
  }
}
export const MappingTable = connect(state => ({
  mappingItems: state.mappingItems,
  chosenCategory: state.chosenCategory
}))(_MappingTable);
