import React from 'react'
import { shallow, configure, render } from 'enzyme'
import Adapter from 'enzyme-adapter-react-16';
configure({adapter: new Adapter()});
import RuleGroupListComponent from '../components/RuleGroupListComponent'
import RuleGroupComponent from '../components/RuleGroupComponent';
test("rule group list component should render properly", ()=>{ 
    shallow(<RuleGroupListComponent ruleGroupList={[{}, {}]}/>)
})

test("given list of rule groups rendering properly", ()=> {
    // we are going to supply 2 rule groups and it should show 2
    const component  = shallow(<RuleGroupListComponent ruleGroupList={[{}, {}]}/>)
    expect(component.find(RuleGroupComponent)).toHaveLength(2)
})

test ("when user clicks on add rule group, then rule group is added", ()=> {
    const component = shallow(<RuleGroupListComponent ruleGroupList={[{}]} />)
    // lets do a click on add rule group button
    component.find('.wl-add-rule-group').simulate('click')
    // now the rule group should be 2
    expect(component.state().ruleGroupList).toHaveLength(2)
})