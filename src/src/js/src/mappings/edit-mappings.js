import React from 'react'
import EditComponent from './components/EditComponent'
import './mappings.css'
import ReactDOM from 'react-dom'
import {Provider} from 'react-redux'
import { createStore } from 'redux'
import {MOCK_INITIAL_STATE, mock_reducers } from './tests/MockStore'
const MOCK_STORE = createStore(mock_reducers, MOCK_INITIAL_STATE)

ReactDOM.render(
    <Provider store={MOCK_STORE}>
        <EditComponent />
    </Provider>,
    document.getElementById("container"))

