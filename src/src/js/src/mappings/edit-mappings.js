import React from 'react'
import EditComponent from './components/EditComponent'
import './mappings.css'
import ReactDOM from 'react-dom'
import {Provider} from 'react-redux'
import { createStore, combineReducers } from 'redux'
import { RuleGroupReducer, PropertyReducer, TitleReducer, NotificationReducer } from './reducers/reducers'
import { ACTIVE_CATEGORY } from './components/CategoryComponent'

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
        ruleFieldOneOptions: options,
        ruleFieldTwoOptions: options,
        ruleLogicFieldOptions: options,
        ruleGroupList: []
    },
    PropertyListData: {
        propertyHeaderCheckboxClicked: false,
        choosenPropertyCategory: ACTIVE_CATEGORY,
        choosenPropertyBulkAction: null,
        fieldTypeHelpTextOptions:editMappingSettings.wl_field_type_help_text_options,
        transformHelpTextOptions:options,
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

