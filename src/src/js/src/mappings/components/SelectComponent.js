/**
 * SelectComponent : component to render the selection box
 * 
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

import React from 'react'

class SelectComponent extends React.Component {
    constructor(props) {
        console.log("Seelct")
        console.log(props)
        super(props)
    }
    render() {
        return (
        <React.Fragment>
            <select value={this.props.value}>
                {
                    this.props.options.map((item, index)=> {
                        
                        return ( 
                            <option key={index} value={item.value}>
                                {item.label}
                            </option> 
                        )
                    })
                }
            </select> 
        </React.Fragment>)
    }
}

export default SelectComponent