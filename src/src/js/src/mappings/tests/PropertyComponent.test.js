/**
 * @since 3.24.0
 * 
 * Tests for Property Component
 */

import React from 'react'
import Adapter from 'enzyme-adapter-react-16';
import { shallow, configure, render } from 'enzyme'
import PropertyComponent from '../components/PropertyComponent'
configure({adapter: new Adapter()});


test("able to render the property component", ()=> {
    shallow(<PropertyComponent propertyData={{}}/>)
})


test("when the property help text is changed, then ui should change", ()=> {
    const component = shallow(<PropertyComponent propertyData={{}} />)
    // lets type some thing on that field
    component.find('.wl-property-help-text')
    .simulate('change', { target: { value: 'something' } })
    // now the state should be changed
    expect(component.state().propertyHelpText).toEqual('something')
})

