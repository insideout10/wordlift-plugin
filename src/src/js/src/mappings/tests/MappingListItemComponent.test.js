/**
 * Test for MappingListItemComponent
 * 
 * @since 3.24.0
 */
import React from "react";
import renderer from 'react-test-renderer';
import MappingListItemComponent from '../components/MappingListItemComponent'

test("check whether mapping list item can be rendered", ()=> {
    renderer.create(<MappingListItemComponent />)
})

test("check provided property rendering properly", ()=> {
   const component = renderer.create(<MappingListItemComponent title="foo" />).root
   expect(component.props.title).toBe("foo")
})