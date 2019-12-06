/**
 * Test for MappingListItemComponent
 * 
 * @since 3.24.0
 */
import React from "react";
import renderer from 'react-test-renderer';


test("check whether mapping list item can be rendered", ()=> {
    renderer.create(<MappingListItemComponent />)
})