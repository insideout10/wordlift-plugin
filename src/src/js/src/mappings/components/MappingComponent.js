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

 class MappingComponent extends React.Component {
     render() {
         return (
            <React.Fragment>
                <h1 className="wp-heading-inline wl-mappings-heading-text">
                    Mappings
                    &nbsp;&nbsp;
                    <button className="button wl-mappings-add-new">
                        <a href="?page=wl_edit_mapping">
                            Add New
                        </a>
                    </button>
                </h1>
                <table className="wp-list-table widefat striped wl-table">
                <thead>
                <tr>
                    <th className="wl-check-column">
                    <input type="checkbox" />
                    </th>
                    <th>
                    <a className="row-title">Title</a>
                    </th>
                </tr>
                </thead>
                <tbody>
                    {
                        // show empty screen when there is no mapping items
                        0 === this.props.mappingItems.length &&
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
                        this.props.mappingItems.map((item, index)=> {
                            return <MappingListItemComponent title={item.title}/>
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
                <label htmlFor="bulk-action-selector-bottom" className="screen-reader-text">Select bulk action</label><select name="action2" id="bulk-action-selector-bottom">
                    <option value={-1}>Bulk Actions</option>
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

 export default MappingComponent