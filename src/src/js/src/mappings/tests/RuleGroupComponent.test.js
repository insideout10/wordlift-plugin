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


 test ("First Rule item should not have remove button", ()=> {
   const wrapper = shallow(<RuleGroupComponent rules={[{},{}]} />)
   // lets check if the first row has the remove button
   const first_rule_row_remove_button = wrapper.find(RuleComponent).at(0).dive().find('.wl-remove-button')
   expect(first_rule_row_remove_button).toHaveLength(0)
   // but the second rule item should have remove button
   const second_rule_row_remove_button =  wrapper.find(RuleComponent).at(1).dive().find('.wl-remove-button')
   expect(second_rule_row_remove_button).toHaveLength(1)
})


test("When remove button clicked should remove the clicked rule item", ()=> {
   const wrapper = shallow(<RuleGroupComponent rules={[{},{}]} />)
   // lets click on the remove button
   wrapper.find(RuleComponent).at(1).dive().find('.wl-remove-button').simulate('click')
   // since remove button is clicked the item would be removed, check the 
   // state of the parent component, it should have only one rule item
   expect(wrapper.state().rules).toHaveLength(1)
})