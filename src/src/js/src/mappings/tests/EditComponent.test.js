/**
 * @since 3.24.0
 * 
 * Tests for Edit Component
 */

import React from 'react'
import Adapter from 'enzyme-adapter-react-16';
import { shallow, configure, mount } from 'enzyme'
configure({adapter: new Adapter()});

import {createStore } from 'redux'
import {Provider} from 'react-redux'
import {MOCK_INITIAL_STATE, mock_reducers } from './MockStore'
import EditComponent from '../components/EditComponent'

var MOCK_STORE  = null
var component = null

beforeEach(() => {
    MOCK_STORE = createStore(mock_reducers, MOCK_INITIAL_STATE)
    component  = mount(
        <Provider store={MOCK_STORE}>
            <EditComponent/>
        </Provider>)
});

test("can render edit component", ()=> {
    mount(
        <Provider store={MOCK_STORE}>
            <EditComponent/>
        </Provider>)
})