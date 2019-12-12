/**
 * SelectComponent : component to render the selection box
 * 
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

import React from 'react'
import { connect } from 'react-redux'
import { CHANGE_RULE_FIELD_VALUE_ACTION } from '../actions/actions'

class SelectComponent extends React.Component {
    constructor(props) {
        super(props)
    }
    handleSelectFieldChange = (event)=> {
        const action = CHANGE_RULE_FIELD_VALUE_ACTION
        action.payload = {}
        action.payload.value = event.target.value
        action.payload.ruleIndex = this.props.ruleIndex
        action.payload.ruleGroupIndex = this.props.ruleGroupIndex
        action.payload.fieldKey = this.props.fieldKey
        console.log(action)
        this.props.dispatch(action) 
    }
    render() {
        return (
        <React.Fragment>
            <select value={this.props.value}
            className={this.props.className}
            onChange={(e)=>this.handleSelectFieldChange(e)}>
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