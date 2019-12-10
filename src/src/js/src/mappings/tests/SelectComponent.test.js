/**
 * @since 3.24.0
 * 
 * tests for select component
 */
import React from 'react'
import { shallow, configure, render } from 'enzyme'
import Adapter from 'enzyme-adapter-react-16';
configure({adapter: new Adapter()});
import SelectComponent from '../components/SelectComponent'
import {options} from './RuleComponent.test'
test("can render the select component", ()=> {
    shallow(<SelectComponent options={options}/>)
})

test("given options rendering properly", ()=> {
    // 3 options passed, 3 options rendered
    const component = shallow(<SelectComponent options={options} className="wl-select-field"/>)
    expect(component.find('.wl-select-field').props().options).toHaveLength(3)
})