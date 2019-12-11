/**
 * @since 3.24.0
 * 
 * Tests for RuleComponent
 */

import React from 'react'
import Adapter from 'enzyme-adapter-react-16';
import { shallow, configure, mount } from 'enzyme'
import { Provider } from 'react-redux'
import RuleComponent from '../components/RuleComponent'
import SelectComponent from '../components/SelectComponent'
configure({adapter: new Adapter()});
import store from '../store/store'

test("should be able to render rule component", ()=> {
    shallow(<RuleComponent store={store} />)
})

test("given options field one rendering correctly", ()=> {
    const wrapper = mount(<RuleComponent store={store}/>)
    // 3 items supplied, so it should be reflected in ui
    expect(wrapper.find('.wl-field-one-select').hostNodes().props().options)
    .toHaveLength(3)
    expect(wrapper.find('.wl-field-two-select').hostNodes().props().options)
    .toHaveLength(3)
    expect(wrapper.find('.wl-field-logic').hostNodes().props().options)
    .toHaveLength(3)
})