/**
 * Components: Selection Box component.
 *
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from "react"
export default class SelectionBoxComponent extends
React.Component {

    constructor(props) {
        super(props)
    }

    render() {
        return (
            <select>
                {this.props.options.map((item,index)=> {
                    return (
                    <option value={item.value} key={index}>
                        { item.text }
                    </option>)
                })}
            </select>
        )
    }

}