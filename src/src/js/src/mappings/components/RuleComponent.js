import React from 'react'
import SelectComponent from './SelectComponent'
import { connect } from 'react-redux'
import { ADD_NEW_RULE } from '../actions/actionTypes'

class RuleComponent extends React.Component {
    constructor(props) {
        super(props)
    }
    handleAddNewRule = (ruleGroupIndex)=> {
        this.props.dispatch(ADD_NEW_RULE, ruleGroupIndex)
    }
    handleDeleteRule = (ruleIndex)=> {
        
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
                         onClick={() => this.handleAddNewRule}>
                             And 
                         </button>
                    </div>
                    {
                        this.props.ruleIndex != 0 && 
                        <div className="wl-col">
                            <button className="button action wl-remove-button"
                            onClick={() => this.handleDeleteRule(this.props.ruleIndex)}>
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