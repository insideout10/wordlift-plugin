import React from 'react'
import EditComponent from './components/edit-component'
import './mappings.css'
import ReactDOM from 'react-dom'
import {Provider} from 'react-redux'
import { createStore, combineReducers } from 'redux'
import { RuleGroupReducer, PropertyReducer, TitleReducer, NotificationReducer } from './reducers/reducers'
import { ACTIVE_CATEGORY } from './components/category-component'

// Set a reference to the WordLift's Edit Mapping settings stored in the window instance.
const editMappingSettings = window["wlEditMappingsConfig"] || {};


const options = [
    { value: 'one', label: 'one' },
    { value: 'two', label: 'two' },
    { value: 'three', label: 'three' }
]

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
        fieldTypeHelpTextOptions:editMappingSettings.wl_field_type_help_text_options,
        transformHelpTextOptions:editMappingSettings.wl_transform_function_options,
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

ReactDOM.render(
    <Provider store={store}>
        <EditComponent />
    </Provider>,
    document.getElementById("container"))

