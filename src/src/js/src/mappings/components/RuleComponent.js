import React from 'react'
import SelectComponent from './SelectComponent'

export default class RuleComponent extends React.Component {
    constructor(props) {

        super(props)
        // we are temporarily adding mock data here
        // to be replaced by redux later
        const options = [
            { value: 'one', label: 'one' },
            { value: 'two', label: 'two' },
            { value: 'three', label: 'three' }
        ]
        this.mock_props = {}
        this.mock_props.ruleFieldOneOptions = options
        this.mock_props.ruleFieldTwoOptions = options
        this.mock_props.ruleLogicFieldOptions = options

    }
    render() {
        return (
                <div className="wl-container wl-rule-container">
                    <div className="wl-col">
                        <SelectComponent options={this.mock_props.ruleFieldOneOptions}
                         className="wl-field-one-select wl-form-select">
                        </SelectComponent>
                    </div>
                    <div className="wl-col">
                        <SelectComponent options={this.mock_props.ruleLogicFieldOptions}
                         className="wl-field-logic wl-form-select">
                        </SelectComponent>
                    </div>
                    <div className="wl-col">
                        <SelectComponent options={this.mock_props.ruleFieldTwoOptions}
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