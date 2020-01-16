/**
 * This file has reducers for mapping list screen
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

 /**
 * Internal dependancies
 */
import { MAPPING_LIST_CHANGED, MAPPING_ITEM_CATEGORY_CHANGED, MAPPING_LIST_BULK_SELECT, MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED, MAPPING_ITEM_SELECTED, BULK_ACTION_SELECTION_CHANGED, MAPPING_ITEMS_BULK_SELECT, MAPPING_LIST_SORT_TITLE_CHANGED } from '../actions/action-types'
import { createReducer } from '@reduxjs/toolkit'
import { BulkOptionValues } from '../components/bulk-action-sub-components'
import { TRASH_CATEGORY, ACTIVE_CATEGORY } from '../components/category-component'
import { SORT_BY_ASC, SORT_BY_DESC } from '../mappings'

const changeCategoryForMappingItems = ( mappingItems, category ) => {
    return mappingItems.map( (item) => {
        item.mapping_status = category
        return item
    })
}

/**
  * Reducer to handle the mapping list section
  */
 export const MappingListReducer = createReducer(null, {
    [ MAPPING_LIST_CHANGED ] : ( state, action ) => {
        console.log( "state changed " )
        state.mappingItems = action.payload.value
    },
    [ MAPPING_ITEM_CATEGORY_CHANGED ] : ( state, action ) => {
        const { mappingId, mappingCategory } = action.payload
        const targetIndex = state.mappingItems
        .map( el => el.mapping_id )
        .indexOf( mappingId )
        state.mappingItems[ targetIndex ].mapping_status = mappingCategory
    },

    [ MAPPING_LIST_BULK_SELECT ] : ( state, action ) => {

        state.mappingItems = state.mappingItems.map((item) => {
            // Select only items in the current choosen category.
            if ( item.mapping_status === state.choosenCategory ) {
                item.isSelected = !item.isSelected
            }
            return item
         })

         state.headerCheckBoxSelected = !state.headerCheckBoxSelected
    },

    [ MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED ] : ( state, action ) => {
        state.choosenCategory = action.payload.categoryName
    },

    [ MAPPING_ITEM_SELECTED ] : ( state, action ) => {
        const { mappingId } = action.payload
        const targetIndex = state.mappingItems
        .map( el => el.mapping_id )
        .indexOf( mappingId )
        state.mappingItems[ targetIndex ].isSelected = !state.mappingItems[ targetIndex ].isSelected
    },

    [ BULK_ACTION_SELECTION_CHANGED ] : ( state, action ) => {
        const { selectedBulkOption } = action.payload
        state.selectedBulkOption = selectedBulkOption
    },

    [ MAPPING_LIST_SORT_TITLE_CHANGED ] : ( state, action ) => {


        if ( state.titleSortBy === SORT_BY_ASC ) {
            state.titleSortBy = SORT_BY_DESC
            state.mappingItems.sort( function( a, b) {
                const x = a.mapping_title;
                const y = b.mapping_title;
                return ((x < y) ? -1 : ((x > y) ? 1 : 0));
            })
        }
        else {
            state.titleSortBy = SORT_BY_ASC
            state.mappingItems.reverse()
        }

    },

    [ MAPPING_ITEMS_BULK_SELECT ] : ( state, action ) => {
       const {duplicateCallBack, updateCallBack } = action.payload
       const selectedItems = state.mappingItems
       .filter( item => true === item.isSelected)
       switch ( state.selectedBulkOption ) {
            case BulkOptionValues.DUPLICATE:
               duplicateCallBack( selectedItems )
               break
            case BulkOptionValues.TRASH:
                // change the category of selected items
                updateCallBack(
                    changeCategoryForMappingItems(
                        selectedItems,
                        TRASH_CATEGORY
                    )
                )
                break
            case BulkOptionValues.RESTORE:
                updateCallBack(
                    changeCategoryForMappingItems(
                        selectedItems,
                        ACTIVE_CATEGORY
                    )
                )
                break
            case BulkOptionValues.DELETE_PERMANENTLY:
                updateCallBack(
                    selectedItems,
                    'DELETE'
                )              
            default:
               break
       }
       state.headerCheckBoxSelected = false
       // Set all to unselected after the operation
       state.mappingItems = state.mappingItems.map( (item) => {
            item.isSelected = false
            return item
       })
    }
})