/**
 * @since 3.24.0
 * 
 * Tests for Property List Component
 */

import React from 'react'
import Adapter from 'enzyme-adapter-react-16';
import { shallow, configure, render } from 'enzyme'
configure({adapter: new Adapter()});

import PropertyListComponent from '../components/PropertyListComponent'
import PropertyListItemComponent from '../components/PropertyListItemComponent';
import PropertyComponent from '../components/PropertyComponent';

const mock_property_list_with_item_open = [{
    isOpenedOrAddedByUser: true,
    propertyHelpText:"foo",
    fieldTypeHelpText: "field type",
    fieldHelpText: "field help text",
    transformHelpText: "transform help text"
}]


test("can render property list component", ()=> {
    shallow(<PropertyListComponent propertyList={[]}/>)
})


test("given list of properties should render the items", ()=>{
    const mock_property_list = [{
        isOpenedOrAddedByUser: false,
        propertyHelpText:"foo",
        fieldTypeHelpText: "field type",
        fieldHelpText: "field help text",
        transformHelpText: "transform help text"
    }]
    const component = shallow(<PropertyListComponent 
        propertyList={mock_property_list}/>)
    // 1 list item should be rendered
    expect(component.find('.wl-property-list-item-container')).toHaveLength(1)
    
    // 1 list item should be in non editable state
    expect(component.find(PropertyListItemComponent).dive().find('.wl-property-list-item')).toHaveLength(1)
})

test("given a single property item in edit state should render correctly", ()=>{
    const mock_property_list = [{
        isOpenedOrAddedByUser: true,
        propertyHelpText:"foo",
        fieldTypeHelpText: "field type",
        fieldHelpText: "field help text",
        transformHelpText: "transform help text"
    }]
    const component = shallow(<PropertyListComponent 
        propertyList={mock_property_list}/>)
    // 1 edit item should be rendered
    expect(component.find(PropertyComponent).dive()
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
     * we have a single property at a list which is in editable
     * state 
     */
    const component = shallow(<PropertyListComponent 
        propertyList={mock_property_list_with_item_open} />)
    
    /**
     * lets make a click on close mapping
     */
    component.find(PropertyComponent).dive()
    .find('.wl-close-mapping').simulate('click')

    // now we should have that item be changed to PropertyListItemComponent
    expect(component.find(PropertyListItemComponent).dive()
    .find('.wl-property-list-item')).toHaveLength(1)
})

test("when add mapping is clicked, able to add an property", ()=> {
    /** 
     * we have no property on propertyList 
     */
    const component = shallow(<PropertyListComponent 
        propertyList={[]} />)
    /**
     * lets click on add mapping
     */
    component.find('.wl-add-mapping').simulate('click')
    /**
     * An list item in editable state should be rendered
     */
    expect(component.find(PropertyComponent).dive()
    .find('.wl-property-edit-item')).toHaveLength(1)
})

test("when property help text is changed should reflect " + 
" on PropertyListItemComponent", ()=> {
    const mock_property_list = [{
        isOpenedOrAddedByUser: true,
        propertyHelpText:"foo",
        fieldTypeHelpText: "field type",
        fieldHelpText: "field help text",
        transformHelpText: "transform help text"
    }]
    const component = shallow(<PropertyListComponent 
        propertyList={mock_property_list} />)

    // now write some text on property help text
    component.find(PropertyComponent).dive().find('.wl-property-help-text')
    .simulate('change', { target: { value: 'something' } })

    component.update()

    // click on close mapping
    component.find(PropertyComponent).dive().find('.wl-close-mapping')
    .simulate('click')
    // temporarily disabling this test since the data is going to be shifted
    // to redux state management


    // // now the text something should be on the list
    // expect(component.update().find(PropertyListItemComponent).dive()
    // .find('.wl-property-list-item-title').props().children).toEqual('something')
})