import React from 'react'
import Adapter from 'enzyme-adapter-react-16';
import { shallow, configure, render } from 'enzyme'
import RuleComponent from '../components/RuleComponent'
configure({adapter: new Adapter()});

// mock options supplied to render on ui
const options = [
    { value: 'one', label: 'one' },
    { value: 'two', label: 'two' },
    { value: 'three', label: 'three' }
]

test("should be able to render rule component", ()=> {
    shallow(<RuleComponent />)
})

test("given options field one rendering correctly", ()=> {
    const wrapper = shallow(<RuleComponent field_one_options={options}/>)
    // 3 items supplied, so it should be reflected in ui
    expect(wrapper.find('.wl-field-one-select').props().options)
    .toHaveLength(3)
})

test("given options logic field rendering correctly", ()=> {
    const wrapper = shallow(<RuleComponent logic_field_options={options}/>)
    // 3 items supplied, so it should be reflected in ui
    expect(wrapper.find('.wl-field-logic').props().options)
    .toHaveLength(3)
})

test("given options field two rendering correctly", ()=> {
    const wrapper = shallow(<RuleComponent field_two_options={options}/>)
    // 3 items supplied, so it should be reflected in ui
    expect(wrapper.find('.wl-field-two-select').props().options)
    .toHaveLength(3)
})