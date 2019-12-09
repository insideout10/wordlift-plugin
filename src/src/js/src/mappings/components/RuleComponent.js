import React from 'react'
import Select from 'react-select'

export default class RuleComponent extends React.Component {
    constructor(props) {
        super(props)
    }
    render() {
        return (
                <div className="wl-rule-container">
                    <div className="wl-col">
                        <Select options={this.props.ruleFieldOneOptions}
                         className="wl-field-one-select wl-form-select">
                        </Select>
                    </div>
                    <div className="wl-col">
                        <Select options={this.props.ruleLogicFieldOptions}
                         className="wl-field-logic wl-form-select">
                        </Select>
                    </div>
                    <div className="wl-col">
                        <Select options={this.props.ruleFieldTwoOptions}
                         className="wl-field-two-select wl-form-select">
                        </Select>
                    </div>
                    <div className="wl-col">
                        <button className="button action wl-and-button"
                         onClick={() => this.props.addNewRuleHandler(this.props.ruleIndex)}>
                             And 
                         </button>
                    </div>
                </div>

        )
    }
}