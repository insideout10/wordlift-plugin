/**
 * This file provides the store for the edit mappings screen.
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.25.0
 */

/**
 * External dependencies.
 */
import {applyMiddleware, combineReducers, createStore} from "redux";
import createSagaMiddleware from "redux-saga";
import logger from "redux-logger";

/**
 * Internal dependencies.
 */
import EditComponentMapping from "../mappings/edit-component-mapping";
import {ACTIVE_CATEGORY} from "../components/category-component";
import {NotificationReducer, PropertyReducer, RuleGroupReducer, TitleReducer} from "../reducers/edit-mapping-reducers";
import editMappingSaga from "./edit-mapping-sagas";

const editMappingSettings = window["wl_edit_mappings_config"] || {};

const INITIAL_STATE = {
    NotificationData: {
        message: "",
        type: ""
    },
    TitleSectionData: {
        title: ""
    },
    RuleGroupData: {
        // Adding filter to determine whether to fetch terms from api or not.
        ruleFieldOneOptions: EditComponentMapping.addNetworkStateToTaxonomyOptions(
            editMappingSettings.wl_rule_field_one_options,
            editMappingSettings.wl_rule_field_two_options
        ),
        ruleFieldTwoOptions: editMappingSettings.wl_rule_field_two_options,
        ruleLogicFieldOptions: editMappingSettings.wl_logic_field_options,
        ruleGroupList: []
    },
    PropertyListData: {
        propertyHeaderCheckboxClicked: false,
        choosenPropertyCategory: ACTIVE_CATEGORY,
        choosenPropertyBulkAction: null,
        fieldTypeHelpTextOptions: editMappingSettings.wl_field_type_options,
        transformHelpTextOptions: editMappingSettings.wl_transform_function_options,
        fieldNameOptions: editMappingSettings.wl_field_name_options,
        propertyList: []
    }
};

const reducers = combineReducers({
    RuleGroupData: RuleGroupReducer,
    PropertyListData: PropertyReducer,
    TitleSectionData: TitleReducer,
    NotificationData: NotificationReducer
});
const sagaMiddleware = createSagaMiddleware();
const editMappingStore = createStore(reducers, INITIAL_STATE, applyMiddleware(sagaMiddleware, logger));
sagaMiddleware.run(editMappingSaga);
export default editMappingStore;
