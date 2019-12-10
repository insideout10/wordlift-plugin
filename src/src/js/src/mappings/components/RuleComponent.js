import React from 'react'
import SelectComponent from './SelectComponent'

export default class RuleComponent extends React.Component {
    constructor(props) {
        super(props)
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
                         onClick={() => this.props.addNewRuleHandler(this.props.ruleIndex)}>
                             And 
                         </button>
                    </div>
                    {
                        this.props.ruleIndex != 0 && 
                        <div className="wl-col">
                            <button className="button action wl-remove-button"
                            onClick={() => this.props.deleteCurrentRuleHandler(this.props.ruleIndex)}>
                                -
                            </button>
                        </div>
                    }
                </div>

        )
    }
}