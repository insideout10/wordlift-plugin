/**
 * This file provides the selectors to get the state
 *
 * @since 3.25.0
 */

/**
 * Return the selected bulk option from the state.
 * @param state Total state of the mapping list ui
 * @returns {String} Returns the selected bulk option
 */
export const getSelectedBulkOption = state => state.selectedBulkOption;

/**
 * Returns all the items selected by the user.
 * @param state Total state of the mapping list ui
 * @returns {Array} Array of selected mapping items.
 */
export const getSelectedMappingItems = state => state.mappingItems.filter(item => true === item.isSelected);

/**
 * Returns all the terms present in the store for the particular taxonomy
 * @param state Total state of edit mapping ui.
 * @param taxonomy Selected taxonomy
 * @return {Array} The array of terms
 */
export function getTermsForTaxonomy(state, taxonomy) {
    return state.RuleGroupData.ruleFieldTwoOptions.filter(e => e.taxonomy === taxonomy)
}
