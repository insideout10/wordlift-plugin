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


export function getTermsForTaxonomy(state, taxonomy) {
    return state.RuleGroupData.ruleFieldTwoOptions.filter(e => e.taxonomy === taxonomy)
}
