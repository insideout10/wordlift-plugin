import { combineReducers, createStore } from 'redux'
import { RuleGroupReducer, PropertyReducer, TitleReducer } from '../reducers/reducers'

const options = [
    { value: 'one', label: 'one' },
    { value: 'two', label: 'two' },
    { value: 'three', label: 'three' }
]

export const MOCK_INITIAL_STATE = {
    TitleSectionData: {
        title: "foo"
    },
    RuleGroupData: {
        ruleFieldOneOptions: options,
        ruleFieldTwoOptions: options,
        ruleLogicFieldOptions: options,
        ruleGroupList: [
            {
                rules: [{}]
            },
            {
                rules:[{}]
            }
        ]
    },
    PropertyListData: {
        fieldTypeHelpTextOptions:options,
        transformHelpTextOptions:options,
        propertyList: [
            {
                isOpenedOrAddedByUser: false,
                propertyHelpText:"foo",
                fieldTypeHelpText: "",
                fieldHelpText: "",
                transformHelpText: ""
            },
            {
                isOpenedOrAddedByUser: true,
                propertyHelpText:"foo",
                fieldTypeHelpText: "",
                fieldHelpText: "",
                transformHelpText: ""
            },
        ]
    }
}


// lets create a mock store with reducers and with initial state
export const mock_reducers = combineReducers({
    RuleGroupData: RuleGroupReducer,
    PropertyListData: PropertyReducer,
    TitleSectionData: TitleReducer
})
