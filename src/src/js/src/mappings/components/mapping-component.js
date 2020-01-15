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
import MappingListItemComponent from './mapping-list-item-component'
import { MAPPING_LIST_CHANGED_ACTION, MAPPING_ITEM_CATEGORY_CHANGED_ACTION, MAPPING_LIST_BULK_SELECT_ACTION, MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION, MAPPING_ITEM_SELECTED_ACTION, MAPPING_ITEMS_BULK_ACTION, BULK_ACTION_SELECTION_CHANGED_ACTION } from '../actions/actions';
import { connect } from 'react-redux'
import CategoryComponent, { ACTIVE_CATEGORY } from './category-component';
import BulkActionComponent from './bulk-action-component';
// Set a reference to the WordLift's Mapping settings stored in the window instance.
const mappingSettings = window["wlMappingsConfig"] || {};

 class MappingComponent extends React.Component {
     componentDidMount() {
         this.getMappingItems()
     }
     bulkActionOptionChangedHandler = ( event ) => {
        const action = BULK_ACTION_SELECTION_CHANGED_ACTION
        action.payload = {
            selectedBulkOption: event.target.value
        }
        this.props.dispatch( action )
     }
     /**
      * Add some keys to mapping items before setting it as
      * state, it is used by ui.
      * @param {Array} mappingItems Mapping items list
      * 
      */
     static applyUiItemFilters( mappingItems ) {
        return mappingItems.map((item)=>(
            {
                ...item,
                // initially no item is selected.
                isSelected: false,
            }
        ))
     }
     /**
      * Convert ui data to api format before posting to api
      * @param {Array} mappingItems Mapping items list
      * 
      */
     static applyApiFilters( mappingItems ) {
         return mappingItems.map((item)=>({
             mapping_id: item.mapping_id,
             mapping_title: item.mapping_title,
             mapping_status: item.mapping_status,
         }))
     }
     /**
      * Extract categories from mappingItems
      * @param {Array} mappingItems Mapping items list
      * @return {Array} List of cateogory objects.
      */
     static extractCategoriesFromMappingItems ( mappingItems ) {
        const categories = {}
        mappingItems.map((item)=> {
            if (!categories.hasOwnProperty(item.mapping_status)) {
                categories[item.mapping_status] = 1
            }
            else {
                categories[item.mapping_status] += 1
            }
        })
        return categories
     }
     /**
      * Selects all the mapping items on the currently active category
      * When triggered on the active, it selects only the active items
      */
     selectAllMappingItems = () => {
        this.props.dispatch( MAPPING_LIST_BULK_SELECT_ACTION )
     }

    switchCategory = ( mappingData, categoryName ) => {
        const action = MAPPING_ITEM_CATEGORY_CHANGED_ACTION
        action.payload = {
            mappingId: mappingData.mapping_id,
            mappingCategory: categoryName
        }
        this.props.dispatch( action )
        // Save Changes to the db
        mappingData.mapping_status = categoryName
        this.updateMappingItems([mappingData])
    }
    // Updates or deletes the mapping items based on the request
    updateMappingItems = ( mappingItems, type = 'PUT') => {
        fetch(mappingSettings.rest_url,
            {
                method: type,
                headers: {
                    "content-type": "application/json",
                    "X-WP-Nonce": mappingSettings.wl_mapping_nonce
                },
                body: JSON.stringify({
                            mappingItems: MappingComponent.applyApiFilters(
                            mappingItems
                        )})  
            }
        )
        .then( response => response.json().then(
            data => {
                // Refresh the screen with the cloned mapping item.
                this.getMappingItems()
            }
        ))
     }

     /**
      * 
      * @param {Array|Object} mappingItems accepts a single 
      * mapping item object or multiple mapping items, clone them by posting
      * to the api endpoint and then refresh the current list.
      */
     duplicateMappingItems = ( mappingItems ) => {
        // If single item is given, construct it to array
        mappingItems = Array.isArray( mappingItems ) ? mappingItems : [ mappingItems ]
        fetch( mappingSettings.rest_url + '/clone', {
            method: 'POST',
            headers: {
                'content-type': 'application/json',
                'X-WP-Nonce': mappingSettings.wl_mapping_nonce
            },
            body: JSON.stringify( { mappingItems: mappingItems } )
        })
        .then( response => response.json().then(
            data => {
                // Refresh the screen with the cloned mapping item.
                this.getMappingItems()
            }
        ))
     }
     /**
      * Fetch the mapping items from api.
      * @return void
      */
     getMappingItems = () => {
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
     /**
      * When the category is selected in the categoryComponent this method
      * is fired.
      * @param {String} category The category choosen by the user
      * @return void 
      */
     categorySelectHandler = ( category ) => {
        const action = MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED_ACTION
        action.payload = {
            categoryName: category
        }
        this.props.dispatch( action )
     }
     /**
      * Called when a mapping item is clicked.
      * @param {Object} mappingData Object represeting single mapping item
      * @return void
      */
     selectMappingItemHandler = ( mappingData ) => {
        const action = MAPPING_ITEM_SELECTED_ACTION
        action.payload = {
            mappingId: mappingData.mapping_id
        }
        console.log( action )
        this.props.dispatch( action )
     }
     bulkActionSubmitHandler = () => {
        const action = MAPPING_ITEMS_BULK_ACTION
        action.payload = {
            duplicateCallBack: this.duplicateMappingItems,
            updateCallBack: this.updateMappingItems,
        }
        this.props.dispatch( MAPPING_ITEMS_BULK_ACTION )
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
                <CategoryComponent 
                    source                = { this.props.mappingItems }
                    categoryKeyName       = 'mapping_status'
                    categories            = { [ 'active', 'trash' ] }
                    categorySelectHandler = { this.categorySelectHandler }
                    choosenCategory       = { this.props.choosenCategory }
                /><br/>
                <table className="wp-list-table widefat striped wl-table">
                    <thead>
                        <tr>
                            <th className="wl-check-column">
                                <input type="checkbox" 
                                    onClick = { this.selectAllMappingItems }
                                    checked = { this.props.headerCheckBoxSelected === true }
                                />
                            </th>
                            <th>
                                <a className="row-title">Title</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {
                            // show empty screen when there is no mapping items
                            0 === this.props.mappingItems
                            .filter( el => el.mapping_status === ACTIVE_CATEGORY )
                            .length && this.props.choosenCategory === ACTIVE_CATEGORY &&
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
                            this.props.mappingItems
                            .filter( el => el.mapping_status === this.props.choosenCategory )
                            .map((item, index)=> {
                                return <MappingListItemComponent
                                selectMappingItemHandler = {
                                    this.selectMappingItemHandler
                                }
                                mappingIndex = {
                                    index
                                }
                                duplicateMappingItemHandler={
                                    this.duplicateMappingItems
                                }
                                
                                deleteMappingItemHandler = {
                                    this.updateMappingItems
                                }

                                switchCategoryHandler= {
                                    this.switchCategory
                                }
                                nonce={mappingSettings.wl_edit_mapping_nonce}
                                mappingData={item}/>
                            })
                        }
                    </tbody>
                    <tfoot>
                        <tr>
                            <th className="wl-check-column">
                                <input type="checkbox" 
                                    onClick = { this.selectAllMappingItems }
                                    checked = { this.props.headerCheckBoxSelected === true }
                                />
                            </th>
                            <th>
                                <a className="row-title">Title</a>
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <div className="wl-container wl-container-full">
                    <BulkActionComponent
                        choosenCategory={this.props.choosenCategory}
                        bulkActionOptionChangedHandler = { this.bulkActionOptionChangedHandler }
                        bulkActionSubmitHandler={ this.bulkActionSubmitHandler }
                    />
                </div>
            </React.Fragment>
         )
     }
 }

const mapStateToProps = function(state){ 
    return {
        mappingItems: state.mappingItems,
        choosenCategory: state.choosenCategory,
        stateObj: state,
        headerCheckBoxSelected: state.headerCheckBoxSelected,
    }
}

export default connect(mapStateToProps)(MappingComponent)
