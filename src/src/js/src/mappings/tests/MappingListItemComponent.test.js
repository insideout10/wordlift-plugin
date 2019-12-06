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

test("provided title renders it on the component", ()=> {
    const component = renderer.create(<MappingListItemComponent title="some title" />).root
    const titleText = component.find(
        (el) => el.type == 'a'
        && el.classList
        && el.classList.contains('wl-mappings-list-item-title')
    ).textContent
    expect(titleText).toBe("some title")
})