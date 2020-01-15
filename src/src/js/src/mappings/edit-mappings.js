/**
 * Shows the list of mapping items in the screen, the user can do 
 * CRUD operations on this ui.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies
 */
import React from 'react'
import ReactDOM from 'react-dom'
import {Provider} from 'react-redux'

/**
 * Internal dependencies
 */
import EditComponent from './components/edit-component'
import './mappings.css'
import { createStore, combineReducers } from 'redux'
import { RuleGroupReducer, PropertyReducer, TitleReducer, NotificationReducer } from './reducers/reducers'
import { ACTIVE_CATEGORY } from './components/category-component'

// Set a reference to the WordLift's Edit Mapping settings stored in the window instance.
const editMappingSettings = window["wl_edit_mappings_config"] || {};

const INITIAL_STATE = {
    NotificationData: {
        message: "",
        type: "",
    },
    TitleSectionData: {
        title: ""
    },
    RuleGroupData: {
        ruleFieldOneOptions: editMappingSettings.wl_rule_field_one_options,
        ruleFieldTwoOptions: editMappingSettings.wl_rule_field_two_options,
        ruleLogicFieldOptions: editMappingSettings.wl_logic_field_options,
        ruleGroupList: []
    },
    PropertyListData: {
        propertyHeaderCheckboxClicked: false,
        choosenPropertyCategory: ACTIVE_CATEGORY,
        choosenPropertyBulkAction: null,
        fieldTypeHelpTextOptions:editMappingSettings.wl_field_type_options,
        transformHelpTextOptions:editMappingSettings.wl_transform_function_options,
        fieldNameOptions: editMappingSettings.wl_field_name_options,
        propertyList: []
    }
}

const reducers = combineReducers({
    RuleGroupData: RuleGroupReducer,
    PropertyListData: PropertyReducer,
    TitleSectionData: TitleReducer,
    NotificationData: NotificationReducer,
})


const store = createStore(reducers, INITIAL_STATE)

window.addEventListener( 'load', () => {
    ReactDOM.render(
        <Provider store={store}>
            <EditComponent />
        </Provider>,
        document.getElementById("wl-edit-mappings-container"))

})