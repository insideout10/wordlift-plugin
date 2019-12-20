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
import { TITLE_CHANGED_ACTION, PROPERTY_LIST_CHANGED_ACTION, RULE_GROUP_LIST_CHANGED_ACTION, MAPPING_HEADER_CHANGED_ACTION, NOTIFICATION_CHANGED_ACTION } from '../actions/actions'
import EditComponentMapping from '../mappings/EditComponentMapping'

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
                    value: EditComponentMapping.mapPropertyAPIKeysToUi( data.property_list )
                }
                this.props.dispatch( property_list_action )

                // Dispatch rule group list changed after applying filters
                const rule_group_list_action = RULE_GROUP_LIST_CHANGED_ACTION
                rule_group_list_action.payload = {
                    value: EditComponentMapping.mapRuleGroupListAPIKeysToUi( data.rule_group_list )
                }
                this.props.dispatch( rule_group_list_action )

            }
        ))
    }

    /**
     * Save the mapping item to the api,
     * Apply some filters, build post object for saving.
     */
    saveMappingItem = () => {
        const postObject = EditComponentMapping.mapStoreKeysToAPI( this.props.stateObject)
        fetch(editMappingSettings.rest_url, {
            method: 'POST',
            headers: {
                "content-type": "application/json",
                "X-WP-Nonce": editMappingSettings.wl_edit_mapping_rest_nonce,           
            },
            body: JSON.stringify(postObject)  
        }).then( response => response.json().then(
            data => {
               const notification_changed_action = NOTIFICATION_CHANGED_ACTION
                notification_changed_action.payload = {
                    message: data.message,
                    type: data.status,
                }
                this.props.dispatch(notification_changed_action)
            }
        ))
    }
    render() {
        return (
            <React.Fragment>
              
                {
                    "" != this.props.notificationData.message &&
                    <div className={'notice notice-' + this.props.notificationData.type + ' is-dismissible'}>
                        <p>{this.props.notificationData.message}</p>
                    </div>
                    
                }
                <h1 className="wp-heading-inline wl-mappings-heading-text">
                    {
                        editMappingSettings.wl_edit_mapping_id === undefined ? (
                            editMappingSettings.wl_add_mapping_text
                        ) : (
                            editMappingSettings.wl_edit_mapping_text
                        )
                    }
                </h1>               
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
                        <td>
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
                        onClick={this.saveMappingItem}
                        disabled={this.props.title === ""}>
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
        notificationData: state.NotificationData,
        stateObject: state,
    }
}

export default connect(mapStateToProps)(EditComponent)