import React from 'react'
import EditComponent from './components/EditComponent'
import MappingComponent from './components/MappingComponent'
import ReactDOM from 'react-dom'
import {Provider} from 'react-redux'
import { createStore } from 'redux'
import {MOCK_INITIAL_STATE, mock_reducers } from './tests/MockStore'
import './mappings.css'
const MOCK_STORE = createStore(mock_reducers, MOCK_INITIAL_STATE)

// ReactDOM.render(
//     <Provider store={MOCK_STORE}>
//         <EditComponent />
//     </Provider>,
//     document.getElementById("container"))

ReactDOM.render(
    <MappingComponent mappingItems={[]}/>,
    document.getElementById("container"))