/**
 * MappingComponent : it displays the entire mapping screen
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from 'react'

/**
 * Internal dependencies
 */
import MappingListItemComponent from './MappingListItemComponent'
import { MAPPING_LIST_CHANGED_ACTION } from '../actions/actions';
import { connect } from 'react-redux'

// Set a reference to the WordLift's Mapping settings stored in the window instance.
const mappingSettings = window["wlMappingsConfig"] || {};

 class MappingComponent extends React.Component {
     state = {
         // The list of mapping items loaded from api
        mappingItems: this.props.mappingItems,
     }
     componentDidMount() {
         this.getMappingItems()
     }
     /**
      * Add some keys to mapping items before setting it as
      * state, it is used by ui.
      * @param {Array} mapping_items Mapping items list
      * 
      */
     static applyUiItemFilters( mapping_items ) {
        return mapping_items.map((item)=>(
            {
                ...item,
                // initially no item is selected.
                is_selected: false,
            }
        ))
     }
     /**
      * Fetch the mapping items from api.
      * @return void
      */
     getMappingItems() {
        fetch(mappingSettings.rest_url,
            {
                method: "GET",
                headers: {
                    "content-type": "application/json",
                    "X-WP-Nonce": mappingSettings.wl_mapping_nonce
                }
            }
        )
        .then(response => response.json().then(
            data => {
                const action = MAPPING_LIST_CHANGED_ACTION
                action.payload  = {    
                    value: MappingComponent.applyUiItemFilters(data)
                }
                this.props.dispatch( action )
            }
        ))
     }
     render() {
         return (
            <React.Fragment>
                <h1 className="wp-heading-inline wl-mappings-heading-text">
                    Mappings
                    &nbsp;&nbsp;
                    <a href="?page=wl_edit_mapping" className="button wl-mappings-add-new">
                        Add New
                    </a>
                </h1>
                <table className="wp-list-table widefat striped wl-table">
                    <thead>
                        <tr>
                            <th className="wl-check-column">
                                <input type="checkbox" onClick={this.selectAllMappingItems} />
                            </th>
                            <th>
                                <a className="row-title">Title</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {
                            // show empty screen when there is no mapping items
                            0 === this.props.mapping_items.length &&
                                <tr>
                                    <td colspan="3">
                                        <div className="wl-container text-center">
                                            No Mapping items found, click on
                                            <b>&nbsp; Add New </b>
                                        </div>
                                    </td>
                                </tr> 
                        }
                        {
                            this.props.mapping_items.map((item, index)=> {
                                return <MappingListItemComponent title={item.mapping_title}
                                nonce={mappingSettings.wl_edit_mapping_nonce}
                                isSelected={item.is_selected}
                                mappingId={item.mapping_id}/>
                            })
                        }
                    </tbody>
                    <tfoot>
                        <tr>
                            <th className="wl-check-column">
                                <input type="checkbox" />
                            </th>
                            <th>
                                <a className="row-title">Title</a>
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <div className="tablenav bottom">
                    <div className="alignleft actions bulkactions">
                        <label htmlFor="bulk-action-selector-bottom" className="screen-reader-text">Select bulk action</label>
                        <select name="action2" id="bulk-action-selector-bottom">
                            <option disabled>Bulk Actions</option>
                            <option value="acfduplicate">Duplicate</option>
                            <option value="trash">Move to Trash</option>
                        </select>
                        <input type="submit" id="doaction2" className="button action" defaultValue="Apply" />
                    </div>
                </div>
            </React.Fragment>
         )
     }
 }

const mapStateToProps = function(state){ 
    return {
        mapping_items: state.mapping_items,
    }
}

export default connect(mapStateToProps)(MappingComponent)
