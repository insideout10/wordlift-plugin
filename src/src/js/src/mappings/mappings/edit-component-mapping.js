/**
 * EditComponentMapping : This maps the ui keys with api response keys and vice versa.
 * 
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

class EditComponentMapping {

    /**
     * @param {Array} rule_list List of rules
     *  Note: if the rule_id are undefined, then dont post it, backend
     *  creates new rule id if there is no id.
     */
    static mapRuleFieldKeysToAPI( rule_list ) {
        return rule_list.map(function(rule) {
            const single_rule = {
                rule_field_one:rule.ruleFieldOneValue,
                rule_field_two:rule.ruleFieldTwoValue,
                rule_logic_field:rule.ruleLogicFieldValue,
            }
            rule.rule_id ? ( single_rule['rule_id'] = rule.rule_id ) : rule.rule_id;
            return single_rule
        })
    }
    /**
     * Convert property list to api format to save the property
     * list propertly
     * @param {Array} property_list List of property items from ui
     */
    static mapPropertyListKeysToAPI( property_list ) {
        return property_list.map((property)=>{
            // Conditionally remove property id, if it is added by user.
            const propertyItem = {
                property_name: property.propertyHelpText,
                field_type_help_text: property.fieldTypeHelpText,
                field_help_text: property.fieldHelpText,
                transform_function: property.transformHelpText,
                property_status: property.property_status,
            }
            // If it is created in the ui, then remove the property id, rest api will detect this and
            // create a new entry for the property.
            if( !property.isPropertyAddedViaUI ) {
                propertyItem.property_id = parseInt( property.property_id )
            }
            return propertyItem

        })
    }

    /**
     * Convert property list API Response to ui format
     * @param {Array} property_list The list of properties
     * from API.
     * @return {Array} New array with mapped keys.
     */
    static mapPropertyAPIKeysToUi( property_list ) {
        return property_list.map((property)=>({
            propertyHelpText:property.property_name,
            fieldTypeHelpText: property.field_type_help_text,
            fieldHelpText: property.field_help_text,
            transformHelpText: property.transform_function,
            property_id: parseInt( property.property_id ),
            property_status: property.property_status,
            isOpenedOrAddedByUser: false,
            isSelectedByUser: false,
            isPropertyAddedViaUI: false,
        }))
    }

    static mapRuleFieldAPIKeysToUi( rule_list ) {
        return rule_list.map((rule) => ({
            ruleFieldOneValue: rule.rule_field_one,
            ruleFieldTwoValue: rule.rule_field_two,
            ruleLogicFieldValue: rule.rule_logic_field,
            rule_id: rule.rule_id
        }))
    }

    /**
     * @param {Array} rule_group_list List of rule group items from api 
     */
    static mapRuleGroupListAPIKeysToUi( rule_group_list ) {
        return rule_group_list.map(( rule_group_item ) => ({
            rule_group_id: rule_group_item.rule_group_id,
            rules: EditComponentMapping.mapRuleFieldAPIKeysToUi(rule_group_item.rules)
        }))
    }
    /**
     * Map Rule group list to api format to save the list.
     * @param {Array} rule_group_list List of rule groups along with rules
     * from ui
     * Note: if the rule_group_ids are undefined, then dont post it, backend
     * creates new rule group if there is no id.
     */
    static mapRuleGroupListKeysToAPI( rule_group_list ) {
        return rule_group_list.map(function ( rule_group_item ){
            const single_rule_group_item = {
                rules: EditComponentMapping.mapRuleFieldKeysToAPI(rule_group_item.rules),
            }
            if ( rule_group_item.rule_group_id ) {
                single_rule_group_item.rule_group_id = rule_group_item.rule_group_id
            }
            return single_rule_group_item
        })
    }
    static mapStoreKeysToAPI( store ) {
        // We create a post object to transform the ui data to Api data
        const postObject = {
            mapping_title: store.TitleSectionData.title,
            property_list: store.PropertyListData.propertyList,
            rule_group_list: store.RuleGroupData.ruleGroupList
        }
        if ( store.TitleSectionData.mapping_id != undefined ) {
            postObject.mapping_id = store.TitleSectionData.mapping_id
        }
        postObject.rule_group_list = EditComponentMapping.mapRuleGroupListKeysToAPI(postObject.rule_group_list)
        postObject.property_list = EditComponentMapping.mapPropertyListKeysToAPI(postObject.property_list)
        return postObject
    } 
}

export default EditComponentMapping