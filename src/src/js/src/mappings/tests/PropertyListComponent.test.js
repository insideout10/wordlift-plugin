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