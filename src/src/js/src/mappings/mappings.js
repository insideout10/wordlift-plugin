import React from 'react'
import MappingComponent from './components/MappingComponent'
import ReactDOM from 'react-dom'
import { createStore } from 'redux'
import {MOCK_INITIAL_STATE, mock_reducers } from './tests/MockStore'
import './mappings.css'
const MOCK_STORE = createStore(mock_reducers, MOCK_INITIAL_STATE)


ReactDOM.render(
    <MappingComponent mappingItems={[]}/>,
    document.getElementById("container"))