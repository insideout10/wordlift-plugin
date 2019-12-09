/**
 * @since 3.24.0
 * 
 * Tests for RuleGroupComponent
 */
import React from 'react'
import { shallow, configure, render } from 'enzyme'
import Adapter from 'enzyme-adapter-react-16';
import RuleGroupComponent from '../components/RuleGroupComponent'
import RuleComponent from '../components/RuleComponent';

configure({adapter: new Adapter()});

 test("can render rule group component", ()=> {
     shallow(<RuleGroupComponent rules={[]} />)
 })

 /**
  * When zero rules are given the rule group should render a single rule.
  */
 test ("given zero rules should render single rule component", ()=> {
    const wrapper = shallow(<RuleGroupComponent rules={[]} />)
    expect(wrapper.state().rules).toHaveLength(1)
 })

 test ("when rule item is clicked then then add a new rule", ()=> {
    const wrapper = shallow(<RuleGroupComponent rules={[{}]} />)
    // lets simulate a click on first rule item
    wrapper.find(RuleComponent).dive().find('.wl-and-button').simulate('click')
    // lets see the state of the rule component
    expect(wrapper.state().rules).toHaveLength(2)
 })