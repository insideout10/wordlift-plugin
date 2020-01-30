/**
 * This file has reducers for mapping list screen
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * Internal dependencies
 */
import {
  MAPPING_LIST_CHANGED,
  MAPPING_ITEM_CATEGORY_CHANGED,
  MAPPING_LIST_BULK_SELECT,
  MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED,
  MAPPING_ITEM_SELECTED,
  BULK_ACTION_SELECTION_CHANGED,
  MAPPING_LIST_SORT_TITLE_CHANGED,
  MAPPINGS_RESET_UI_AFTER_BULK_APPLY
} from "../actions/action-types";
import { createReducer } from "@reduxjs/toolkit";
import { SORT_BY_ASC, SORT_BY_DESC } from "../constants";

/**
 * Reducer to handle the mapping list section
 */
export const MappingListReducer = createReducer(null, {
  [MAPPING_LIST_CHANGED]: (state, action) => {
    state.mappingItems = action.payload.value;
  },
  [MAPPING_ITEM_CATEGORY_CHANGED]: (state, action) => {
    const { mappingId, mappingCategory } = action.payload;
    const targetIndex = state.mappingItems.map(el => el.mapping_id).indexOf(mappingId);
    state.mappingItems[targetIndex].mappingStatus = mappingCategory;
  },

  [MAPPING_LIST_BULK_SELECT]: (state, action) => {
    state.mappingItems = state.mappingItems.map(item => {
      // Select only items in the current chosenCategory.
      if (item.mappingStatus === state.chosenCategory) {
        item.isSelected = !item.isSelected;
      }
      return item;
    });

    state.headerCheckBoxSelected = !state.headerCheckBoxSelected;
  },

  [MAPPING_LIST_CHOOSEN_CATEGORY_CHANGED]: (state, action) => {
    state.chosenCategory = action.payload.categoryName;
  },

  [MAPPING_ITEM_SELECTED]: (state, action) => {
    const { mappingId } = action.payload;
    const targetIndex = state.mappingItems.map(el => el.mappingId).indexOf(mappingId);
    state.mappingItems[targetIndex].isSelected = !state.mappingItems[targetIndex].isSelected;
  },

  [BULK_ACTION_SELECTION_CHANGED]: (state, action) => {
    const { selectedBulkOption } = action.payload;
    state.selectedBulkOption = selectedBulkOption;
  },

  [MAPPING_LIST_SORT_TITLE_CHANGED]: (state, action) => {
    if (state.titleSortBy === SORT_BY_ASC) {
      state.titleSortBy = SORT_BY_DESC;
      state.mappingItems
        .sort(function(a, b) {
          const x = a.mapping_title;
          const y = b.mapping_title;
          return x < y ? -1 : x > y ? 1 : 0;
        })
        .reverse();
      state.titleIcon = "dashicons-arrow-down";
    } else {
      state.titleSortBy = SORT_BY_ASC;
      state.titleIcon = "dashicons-arrow-up";
      state.mappingItems.sort(function(a, b) {
        const x = a.mapping_title;
        const y = b.mapping_title;
        return x < y ? -1 : x > y ? 1 : 0;
      });
    }
  },
  [MAPPINGS_RESET_UI_AFTER_BULK_APPLY]: (state, action) => {
    state.headerCheckBoxSelected = false;
    // Set all to unselected after the operation
    state.mappingItems = state.mappingItems.map(item => {
      item.isSelected = false;
      return item;
    });
  }
});
