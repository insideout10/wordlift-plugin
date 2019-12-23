import React from 'react'
import MappingComponent from './components/MappingComponent'
import ReactDOM from 'react-dom'
import {Provider} from 'react-redux'
import { createStore } from 'redux'
import './mappings.css'
import { MappingListReducer } from './reducers/mapping_list_reducers'


const MAPPINGS_INITIAL_STATE = {
    mapping_items: [],
    choosen_category: 'active',
}

const store = createStore(MappingListReducer, MAPPINGS_INITIAL_STATE)

ReactDOM.render(
    <Provider store={store}>
        <MappingComponent />
    </Provider>,
    document.getElementById("container"))
