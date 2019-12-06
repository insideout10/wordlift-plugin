/**
 * Test for SelectionBoxComponent
 * 
 * @since 3.24.0
 */
import React from "react";
import renderer from 'react-test-renderer';
import SelectionBoxComponent from '../components/SelectionBoxComponent'

test("check whether selection box component can be rendered", ()=> {
    renderer.create(<SelectionBoxComponent options={[]}/>)
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
    expect(optionCount).toBe(1)
})

test("provided options and selected option, should select correct option",
    ()=>{
        const options = [
            {
                text:"foo",
                value: "bar"
            },
            {
                text: "some foo",
                value: "some value"
            }
        ]
        const componentRoot = renderer
            .create(<SelectionBoxComponent options={options} 
                selectedOption="some value"/>).root
                
        // now the component should have selected 'some value'
        const optionValue = componentRoot.findAll(
            (el) => el.type == 'option'
            && el.props.selected
        ).value
        
        expect(optionValue).toBe("some value")

    }
)