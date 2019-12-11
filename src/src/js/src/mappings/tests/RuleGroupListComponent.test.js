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

test("rule group list component should render properly", ()=>{
    const MOCK_STORE = createStore(mock_reducers, MOCK_INITIAL_STATE) 
    shallow(<RuleGroupListComponent store={MOCK_STORE}/>)
})

test("given list of rule groups rendering properly", ()=> {
    const MOCK_STORE = createStore(mock_reducers, MOCK_INITIAL_STATE)
    const component  = mount(
                    <Provider store={MOCK_STORE}>
                        <RuleGroupListComponent/>
                    </Provider>)
    expect(component.find(RuleGroupComponent)).toHaveLength(2)

})

test ("when user clicks on add rule group, then rule group is added", ()=> {
    const MOCK_STORE = createStore(mock_reducers, MOCK_INITIAL_STATE)
    const component = mount(
                            <Provider store={MOCK_STORE}>
                                <RuleGroupListComponent/>
                            </Provider>
                        )
    // lets do a click on add rule group button
    component.find('.wl-add-rule-group').simulate('click')
    // now the rule group should be 2
    expect(MOCK_STORE.getState().RuleGroupData.ruleGroupList)
    .toHaveLength(3)
})