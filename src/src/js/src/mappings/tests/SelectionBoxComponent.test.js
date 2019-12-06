/**
 * Test for SelectionBoxComponent
 * 
 * @since 3.24.0
 */
import React from "react";
import renderer from 'react-test-renderer';
import SelectionBoxComponent from '../components/SelectionBoxComponent'

test("check whether selection box component can be rendered", ()=> {
    renderer.create(<SelectionBoxComponent />)
})
