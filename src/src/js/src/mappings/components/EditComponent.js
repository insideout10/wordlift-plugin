/**
 * EditComponent : it displays the edit section for the mapping item
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from 'react'
import { connect } from 'react-redux'

/**
 * Internal dependencies
 */
import RuleGroupListComponent from './RuleGroupListComponent'
import PropertyListComponent from './PropertyListComponent'
import { TITLE_CHANGED_ACTION, PROPERTY_LIST_CHANGED_ACTION, RULE_GROUP_LIST_CHANGED_ACTION, MAPPING_HEADER_CHANGED_ACTION } from '../actions/actions'

// Set a reference to the WordLift's Edit Mapping settings stored in the window instance.
const editMappingSettings = window["wlEditMappingsConfig"] || {};

 class EditComponent extends React.Component {

    constructor(props) {
        super(props)
    }
    /**
     * When the title is changed, this method saves it in the redux store.
     * @param {Object} event The event which is fired when mapping title changes
     */
    handleTitleChange = ( event )=> {
        const action = TITLE_CHANGED_ACTION
        action.payload = {
            value: event.target.value
        }
        this.props.dispatch(action)
    }
    componentDidMount() {
        if (editMappingSettings.wl_edit_mapping_id != undefined) {
            this.getMappingItemByMappingId()
        }
    }

    /**
     * Get edit mapping item if the mapping id is supplied
     * via the url
     */
    getMappingItemByMappingId() {
        const url  = editMappingSettings.rest_url + "/" + editMappingSettings.wl_edit_mapping_id
        fetch(url,
            {
                method: "GET",
                headers: {
                    "content-type": "application/json",
                    "X-WP-Nonce": editMappingSettings.wl_edit_mapping_rest_nonce
                }
            }
        )
        .then(response => response.json().then(
            data=> {
                // Dispatch title changed
                const mapping_header_action = MAPPING_HEADER_CHANGED_ACTION
                mapping_header_action.payload = {
                    title: data.mapping_title,
                    mapping_id: data.mapping_id,
                }
                this.props.dispatch(mapping_header_action, data.mapping_title)

                //Dispatch property list changed after applying filters
                const property_list_action = PROPERTY_LIST_CHANGED_ACTION
                property_list_action.payload = {
                    value: EditComponent.mapPropertyAPIKeysToUi( data.property_list )
                }
                this.props.dispatch( property_list_action )

                // Dispatch rule group list changed after applying filters
                const rule_group_list_action = RULE_GROUP_LIST_CHANGED_ACTION
                rule_group_list_action.payload = {
                    value: EditComponent.mapRuleGroupListAPIKeysToUi( data.rule_group_list )
                }
                this.props.dispatch( rule_group_list_action )

            }
        ))
    }

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
        return property_list.map((property)=>({
            property_help_text: property.propertyHelpText,
            field_type_help_text: property.fieldTypeHelpText,
            field_help_text: property.fieldHelpText,
            transform_help_text: property.transformHelpText,
            property_id: property.property_id,
        }))
    }

    /**
     * Convert property list API Response to ui format
     * @param {Array} property_list The list of properties
     * from API.
     * @return {Array} New array with mapped keys.
     */
    static mapPropertyAPIKeysToUi( property_list ) {
        return property_list.map((property)=>({
            propertyHelpText:property.property_help_text,
            fieldTypeHelpText: property.field_type_help_text,
            fieldHelpText: property.field_help_text,
            transformHelpText: property.transform_help_text,
            property_id: property.property_id,
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
            rules: EditComponent.mapRuleFieldAPIKeysToUi(rule_group_item.rules)
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
                rules: EditComponent.mapRuleFieldKeysToAPI(rule_group_item.rules),
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
        postObject.rule_group_list = EditComponent.mapRuleGroupListKeysToAPI(postObject.rule_group_list)
        postObject.property_list = EditComponent.mapPropertyListKeysToAPI(postObject.property_list)
        return postObject
    } 
    /**
     * Save the mapping item to the api,
     * Apply some filters, build post object for saving.
     */
    saveMappingItem = () => {
        const postObject = EditComponent.mapStoreKeysToAPI( this.props.stateObject)
        fetch(editMappingSettings.rest_url, {
            method: 'POST',
            headers: {
                "content-type": "application/json",
                "X-WP-Nonce": editMappingSettings.wl_edit_mapping_rest_nonce,           
            },
            body: JSON.stringify(postObject)  
        })
    }
    render() {
        return (
            <React.Fragment>
                 <br /> <br />
                <input type="text"
                    className="wl-form-control wl-input-class"
                    value={this.props.title}
                    onChange={(e)=> {this.handleTitleChange(e)}}/>
                    <br /> <br />
                <table className="wp-list-table widefat striped wl-table wl-container-full">
                    <thead>
                    <tr>
                        <td colSpan={0}>
                        <b>Rules</b> 
                        </td>
                        <td colSpan={2}>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td className="wl-bg-light wl-description">
                                Here we show the help text
                            </td>
                            <td>
                                <div>
                                    <b>Use the mapping if</b>
                                    <RuleGroupListComponent />
                                </div>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br/><br/>
                <PropertyListComponent />
                <br/>
                <div className="wl-container wl-container-full">
                    <div className="wl-col">
                        <select  className="form-control">
                            <option value="-1">Bulk Actions</option>
                            <option value="duplicate">Duplicate</option>
                            <option value="trash">Move to Trash</option>
                        </select>
                    </div>
                    <div className="wl-col">
                        <button className="button action"> Apply </button>
                    </div>
                    <div className="wl-col wl-align-right">
                        <button className="button action" 
                        onClick={this.saveMappingItem}>
                            Save
                        </button>
                    </div>
                </div>
            </React.Fragment>
        )
    }
}

const mapStateToProps = function( state ) {
    return {
        title: state.TitleSectionData.title,
        stateObject: state,
    }
}

export default connect(mapStateToProps)(EditComponent)