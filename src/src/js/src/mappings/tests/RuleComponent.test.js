/**
 * @since 3.24.0
 * 
 * Tests for RuleComponent
 */

import React from 'react'
import Adapter from 'enzyme-adapter-react-16';
import { shallow, configure, render } from 'enzyme'
import RuleComponent from '../components/RuleComponent'
configure({adapter: new Adapter()});

// mock options supplied to render on ui
export const options = [
    { value: 'one', label: 'one' },
    { value: 'two', label: 'two' },
    { value: 'three', label: 'three' }
]

test("should be able to render rule component", ()=> {
    const addNewRuleMockHandler = jest.fn()
    shallow(<RuleComponent addNewRuleHandler={addNewRuleMockHandler}/>)
})

test("given options field one rendering correctly", ()=> {
    const addNewRuleMockHandler = jest.fn()
    const wrapper = shallow(<RuleComponent 
        ruleFieldOneOptions={options} 
        addNewRuleHandler={addNewRuleMockHandler}/>)
    // 3 items supplied, so it should be reflected in ui
    expect(wrapper.find('.wl-field-one-select').props().options)
    .toHaveLength(3)
})

test("given options logic field rendering correctly", ()=> {
    const addNewRuleMockHandler = jest.fn()
    const wrapper = shallow(<RuleComponent 
        ruleLogicFieldOptions={options} 
        addNewRuleHandler={addNewRuleMockHandler}/>)   
    // 3 items supplied, so it should be reflected in ui
    expect(wrapper.find('.wl-field-logic').props().options)
    .toHaveLength(3)
})

test("given options field two rendering correctly", ()=> {
    const addNewRuleMockHandler = jest.fn()
    const wrapper = shallow(<RuleComponent 
        ruleFieldTwoOptions={options} 
        addNewRuleHandler={addNewRuleMockHandler}/>)  
    // 3 items supplied, so it should be reflected in ui
    expect(wrapper.find('.wl-field-two-select').props().options)
    .toHaveLength(3)
})

test("when and button clicked, add additional row below", ()=> {
    const addNewRuleMockHandler = jest.fn()
    const wrapper = shallow(<RuleComponent 
        addNewRuleHandler={addNewRuleMockHandler}/>)
    wrapper.find('.wl-and-button').simulate('click')
    expect(addNewRuleMockHandler.mock.calls.length).toEqual(1)
})