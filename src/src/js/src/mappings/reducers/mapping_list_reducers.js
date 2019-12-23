/**
 * This file has reducers for mapping list screen
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

 /**
 * Internal dependancies
 */
import { MAPPING_LIST_CHANGED, MAPPING_ITEM_CATEGORY_CHANGED, MAPPING_LIST_BULK_SELECT, MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED, MAPPING_ITEM_SELECTED } from '../actions/actionTypes'
import { createReducer } from '@reduxjs/toolkit'

/**
  * Reducer to handle the mapping list section
  */
 export const MappingListReducer = createReducer(null, {
    [ MAPPING_LIST_CHANGED ] : ( state, action ) => {
        console.log( "state changed " )
        state.mapping_items = action.payload.value
    },
    [ MAPPING_ITEM_CATEGORY_CHANGED ] : ( state, action ) => {
        const { mappingId, mappingCategory } = action.payload
        const targetIndex = state.mapping_items
        .map( el => el.mapping_id )
        .indexOf( mappingId )
        state.mapping_items[ targetIndex ].mapping_status = mappingCategory
    },

    [ MAPPING_LIST_BULK_SELECT ] : ( state, action ) => {
        state.mapping_items = state.mapping_items.map((item) => {
            // Select only items in the current choosen category.
            if ( item.mapping_status === state.choosen_category ) {
                item.is_selected = !item.is_selected
            }
            return item
         })
    },

    [ MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED ] : ( state, action ) => {
        state.choosen_category = action.payload.categoryName
    },

    [ MAPPING_ITEM_SELECTED ] : ( state, action ) => {
        const { mappingId } = action.payload
        const targetIndex = state.mapping_items
        .map( el => el.mapping_id )
        .indexOf( mappingId )
        state.mapping_items[ targetIndex ].is_selected = !state.mapping_items[ targetIndex ].is_selected
    }
})