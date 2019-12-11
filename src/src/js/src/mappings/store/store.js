// mock options supplied to render on ui
const options = [
    { value: 'one', label: 'one' },
    { value: 'two', label: 'two' },
    { value: 'three', label: 'three' }
]

import { createStore } from 'redux'


function reducer () {
    return {
        ruleFieldOneOptions: options,
        ruleFieldTwoOptions: options,
        ruleLogicFieldOptions: options,
    }
}


const store = createStore(reducer)

export default store