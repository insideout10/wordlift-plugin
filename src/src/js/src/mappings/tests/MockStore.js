import { combineReducers, createStore } from 'redux'
import { RuleGroupReducer } from '../reducers/reducers'

const options = [
    { value: 'one', label: 'one' },
    { value: 'two', label: 'two' },
    { value: 'three', label: 'three' }
]

export const MOCK_INITIAL_STATE = {
    RuleGroupData: {
        ruleFieldOneOptions: options,
        ruleFieldTwoOptions: options,
        ruleLogicFieldOptions: options,
        ruleGroupList: [{},{}]
    }
}


// lets create a mock store with reducers and with initial state
export const mock_reducers = combineReducers({
    RuleGroupData: RuleGroupReducer
})
