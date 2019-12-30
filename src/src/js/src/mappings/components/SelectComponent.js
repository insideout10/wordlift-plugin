/**
 * SelectComponent : component to render the selection box
 * 
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

/**
 * External dependencies
 */
import React from 'react'
import { connect } from 'react-redux'

/**
 * Internal dependencies
 */
import { CHANGE_RULE_FIELD_VALUE_ACTION } from '../actions/actions'

class SelectComponent extends React.Component {
    constructor(props) {
        super(props)
    }
    render() {
        return (
        <React.Fragment>
            <select value={this.props.value}
            className={this.props.className}
            onChange={this.props.onChange}>
                <option selected={true} disabled={true} value=''>
                    Select one
                </option>
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

const mapStateToProps = function (state) {
    return  {

    }
}
export default connect(mapStateToProps)(SelectComponent)