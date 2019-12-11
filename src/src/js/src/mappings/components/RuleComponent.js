/**
 * RuleComponent : Displays a single rule 
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.24.0
 */

import React from 'react'
import SelectComponent from './SelectComponent'
import { connect } from 'react-redux'

import { ADD_NEW_RULE_ACTION, DELETE_RULE_ACTION } from '../actions/actions'

class RuleComponent extends React.Component {
    constructor(props) {
        super(props)
    }
    handleAddNewRule = (ruleGroupIndex, ruleIndex)=> {
        const action = ADD_NEW_RULE_ACTION
        action.payload = {}
        action.payload.ruleGroupIndex = ruleGroupIndex
        action.payload.ruleIndex = ruleIndex
        this.props.dispatch(action)
    }
    handleDeleteRule = (ruleGroupIndex, ruleIndex)=> {
        const action = DELETE_RULE_ACTION
        action.payload = {}
        action.payload.ruleGroupIndex = ruleGroupIndex
        action.payload.ruleIndex = ruleIndex
        this.props.dispatch(action)
    }
    render() {
        return (
                <div className="wl-container wl-rule-container">
                    <div className="wl-col">
                        <SelectComponent options={this.props.ruleFieldOneOptions}
                         className="wl-field-one-select wl-form-select">
                        </SelectComponent>
                    </div>
                    <div className="wl-col">
                        <SelectComponent options={this.props.ruleLogicFieldOptions}
                         className="wl-field-logic wl-form-select">
                        </SelectComponent>
                    </div>
                    <div className="wl-col">
                        <SelectComponent options={this.props.ruleFieldTwoOptions}
                         className="wl-field-two-select wl-form-select">
                        </SelectComponent>
                    </div>
                    <div className="wl-col">
                        <button className="button action wl-and-button"
                         onClick={() => this.handleAddNewRule(this.props.ruleGroupIndex, this.props.ruleIndex) }>
                             And 
                         </button>
                    </div>
                    {
                        this.props.ruleGroupIndex != 0 &&
                        <div className="wl-col">
                            <button className="button action wl-remove-button"
                            onClick={() => this.handleDeleteRule(this.props.ruleGroupIndex, this.props.ruleIndex)}>
                                -
                            </button>
                        </div>
                    }
                </div>

        )
    }
}

const mapStateToProps = state => ({
    ruleFieldOneOptions: state.RuleGroupData.ruleFieldOneOptions,
    ruleFieldTwoOptions: state.RuleGroupData.ruleFieldTwoOptions,
    ruleLogicFieldOptions: state.RuleGroupData.ruleLogicFieldOptions
})

export default connect(mapStateToProps)(RuleComponent)