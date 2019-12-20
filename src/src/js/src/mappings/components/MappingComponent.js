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
import { MAPPING_LIST_CHANGED_ACTION, CATEGORY_OBJECT_CHANGED_ACTION, CATEGORY_ITEMS_LIST_CHANGED_ACTION } from '../actions/actions';
import { connect } from 'react-redux'
import { MAPPING_LIST_CHANGED, CATEGORY_ITEMS_LIST_CHANGED } from '../actions/actionTypes';

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
      * Extract categories from mapping_items
      * @param {Array} mapping_items Mapping items list
      * @return {Array} List of cateogory objects.
      */
     static extractCategoriesFromMappingItems ( mapping_items ) {
        const categories = {}
        mapping_items.map((item)=> {
            if (!categories.hasOwnProperty(item.mapping_status)) {
                categories[item.mapping_status] = 1
            }
            else {
                categories[item.mapping_status] += 1
            }
        })
        return categories
     }
     selectAllMappingItems = () => {
         const action = MAPPING_LIST_CHANGED_ACTION
         action.payload = {
             value: this.props.mapping_items.map((item) => {
                item.is_selected = !item.is_selected
                return item
             })
         }
         this.props.dispatch( MAPPING_LIST_CHANGED_ACTION )
     }
     /**
      * Switches category on click of category item.
      * @param {String} category Category which needes to be switched 
      */
     switchCategory = ( category ) => {
        const category_items_changed = CATEGORY_ITEMS_LIST_CHANGED_ACTION
        category_items_changed.payload = {
            value: this.props.mapping_items.filter((item)=> {
                return item.mapping_status === category
            })
        }
        this.props.dispatch( category_items_changed )
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
                const category_action = CATEGORY_OBJECT_CHANGED_ACTION
                category_action.payload = {
                    value: MappingComponent.extractCategoriesFromMappingItems( data )
                }
                const category_items_changed = CATEGORY_ITEMS_LIST_CHANGED_ACTION
                category_items_changed.payload = {
                    value: MappingComponent.applyUiItemFilters(data)
                }
                this.props.dispatch( category_items_changed )
                this.props.dispatch( category_action ) 
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
                <p>
                    {
                        Object.keys(this.props.categories).map((key) => {
                        return (
                            <a href="#" onClick={()=> { this.switchCategory(key) }}> 
                                {key} ({this.props.categories[key]})
                            </a>)
                        })
                    }
                </p>
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
                            0 === this.props.category_items.length &&
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
                            this.props.category_items.map((item, index)=> {
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

                        <select name="action2" id="bulk-action-selector-bottom">
                            <option disabled selected>Bulk Actions</option>
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
        categories: state.categories,
        category_items: state.category_items,
    }
}

export default connect(mapStateToProps)(MappingComponent)
