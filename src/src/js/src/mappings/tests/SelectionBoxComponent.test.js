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

test("provided options via property, then renders the options", ()=> {
    const options = [
        {
            text:"foo",
            value: "bar"
        },
    ]
    const componentRoot = renderer
    .create(<SelectionBoxComponent options={options} />).root

    // now check if the options got rendered
    const optionCount = componentRoot.findAll(
        (el) => el.type == 'option'
    ).length
    // the options should be 1
    expect(optionCount).toBe(1)
})
