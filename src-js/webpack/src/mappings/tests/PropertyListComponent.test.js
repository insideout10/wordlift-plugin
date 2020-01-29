/**
 * @since 3.24.0
 * 
 * Tests for Property List Component
 */

import React from 'react'
import Adapter from 'enzyme-adapter-react-16';
import { configure, mount } from 'enzyme'
configure({adapter: new Adapter()});

import PropertyListComponent from '../components/PropertyListComponent'
import PropertyListItemComponent from '../components/PropertyListItemComponent';
import PropertyComponent from '../components/PropertyComponent';

import {createStore } from 'redux'
import {Provider} from 'react-redux'
import {MOCK_INITIAL_STATE, mock_reducers } from './MockStore'


var MOCK_STORE  = null
var component = null
// reset the store after every test
beforeEach(() => {
    MOCK_STORE = createStore(mock_reducers, MOCK_INITIAL_STATE)
    component  = mount(
        <Provider store={MOCK_STORE}>
            <PropertyListComponent/>
        </Provider>)
});



test("given list of properties should render the items", ()=>{

    // 1 list item should be rendered
    expect(component.find('.wl-property-list-item-container')).toHaveLength(2)
    
    // 1 list item should be in non editable state
    expect(component.find(PropertyListItemComponent).find('.wl-property-list-item'))
    .toHaveLength(1)
})

test("given a single property item in edit state should render correctly", ()=>{
    // 1 edit item should be rendered
    expect(component.find(PropertyComponent)
    .find('.wl-property-edit-item')).toHaveLength(1)
})


test("when close mapping on edit property list item is clicked,"  + 
" should convert back to list item", ()=> {
    /**
     * we are going to create a mock property list which
     * has a list item in editable state, so we simulate a click
     * on close mapping button which should return it to 
     * list item state
     */

    
    /**
     * lets make a click on close mapping
     */
    component.find(PropertyComponent)
    .find('.wl-close-mapping').simulate('click')

    // now we should have that item be changed to PropertyListItemComponent
    expect(component.find(PropertyListItemComponent)
    .find('.wl-property-list-item')).toHaveLength(2)
})

test("when add mapping is clicked, able to add an property", ()=> {

    /**
     * lets click on add mapping
     */
    component.find('.wl-add-mapping').simulate('click')
    /**
     * An list item in editable state should be rendered
     */
    expect(component.find(PropertyComponent)
    .find('.wl-property-edit-item')).toHaveLength(2)
})

test("when property help text is changed should reflect " + 
" on PropertyListItemComponent", ()=> {
    // now write some text on property help text
    component.find(PropertyComponent).find('.wl-property-help-text')
    .simulate('change', { target: { value: 'something' } })
    // click on close mapping
    component.find(PropertyComponent).find('.wl-close-mapping')
    .simulate('click')

    // check the state
    expect(component.find(PropertyListItemComponent)
    .at(1).find('.wl-property-list-item-title').props().children)
    .toEqual("something") 
    
})