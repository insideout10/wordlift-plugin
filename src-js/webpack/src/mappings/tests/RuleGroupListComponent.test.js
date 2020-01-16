/**
 * This file has tests for RuleGroupListComponent
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

import React from 'react'
import { shallow, configure, mount } from 'enzyme'
import Adapter from 'enzyme-adapter-react-16';
configure({adapter: new Adapter()});
import RuleGroupListComponent from '../components/RuleGroupListComponent'
import RuleGroupComponent from '../components/RuleGroupComponent';
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
            <RuleGroupListComponent/>
        </Provider>)
});

test("rule group list component should render properly", ()=>{
    shallow(<RuleGroupListComponent store={MOCK_STORE}/>)
})

test("given list of rule groups rendering properly", ()=> {
    expect(component.find(RuleGroupComponent)).toHaveLength(2)

})

test ("when user clicks on add rule group, then rule group is added", ()=> {
    // lets do a click on add rule group button
    component.find('.wl-add-rule-group').simulate('click')
    // now the rule group should be 3
    expect(MOCK_STORE.getState().RuleGroupData.ruleGroupList)
    .toHaveLength(3)
})

test("when user clicks on the and button on first rule group it should add a rule", ()=>{
   // and button clicked on first rule of first tule group component
    component.find('.wl-and-button').at(0).simulate('click')
    expect(MOCK_STORE.getState()
    .RuleGroupData.ruleGroupList[0]
    .rules).toHaveLength(2)
})

test("when there is no rule in the second rule group" +
 "after removal then remove that rule group", ()=> {
     // we removed the only one rule from the second rule group
    component.find('.wl-remove-button').at(0).simulate('click')
    expect(MOCK_STORE.getState()
    .RuleGroupData.ruleGroupList).toHaveLength(1)
})