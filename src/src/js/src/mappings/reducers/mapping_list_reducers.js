/**
 * This file has reducers for mapping list screen
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

 /**
 * Internal dependancies
 */
import { MAPPING_LIST_CHANGED, CATEGORY_OBJECT_CHANGED, CATEGORY_ITEMS_LIST_CHANGED } from '../actions/actionTypes'
import { createReducer } from '@reduxjs/toolkit'

/**
  * Reducer to handle the mapping list section
  */
 export const MappingListReducer = createReducer(null, {
    [ MAPPING_LIST_CHANGED ] : ( state, action ) => {
        state.mapping_items = action.payload.value
    },
})