/**
 * This file provides the filters before adding data to edit mapping store.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

class EditMappingStoreFilters {
    /**
     * Construct the rule field one options from the server data.
     * @param ruleFieldOneOptions
     * @return {*[]|*}
     */
    static filterRuleFieldOneData(ruleFieldOneOptions) {
        if (!Array.isArray(ruleFieldOneOptions)) {
            return [];
        }
        return ruleFieldOneOptions.map(item => ({
            apiSource: item.api_source,
            label: item.label,
            value: item.value
        }));
    }

    /**
     * Construct the rule field two options from server data.
     * @param ruleFieldTwoOptions
     * @return {*[]|*}
     */
    static filterRuleFieldTwoData(ruleFieldTwoOptions) {
        if (!Array.isArray(ruleFieldTwoOptions)) {
            return [];
        }
        return ruleFieldTwoOptions.map(item => ({
            parentValue: item.parent_value,
            label: item.label,
            value: item.value
        }));
    }
}

export default EditMappingStoreFilters;
