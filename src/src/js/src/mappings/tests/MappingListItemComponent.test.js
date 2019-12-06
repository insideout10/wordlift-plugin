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