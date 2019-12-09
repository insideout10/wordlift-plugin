import React from 'react'
import Select from 'react-select'

export default class RuleComponent extends React.Component {
    constructor(props) {
        super(props)
    }
    render() {
        return (
                <div className="wl-container">
                    <div className="wl-col">
                        <Select options={this.props.rule_field_one_options}
                         className="wl-field-one-select wl-form-select">
                        </Select>
                    </div>
                    <div className="wl-col">
                        <Select options={this.props.rule_logic_field_options}
                         className="wl-field-logic wl-form-select">
                        </Select>
                    </div>
                    <div className="wl-col">
                        <Select options={this.props.rule_field_two_options}
                         className="wl-field-two-select wl-form-select">
                        </Select>
                    </div>
                    <div className="wl-col">
                        <button className="button action wl-and-button"
                         onClick={this.props.addNewRuleHandler(this.props.rule_index)}>
                             And 
                         </button>
                    </div>
                </div>

        )
    }
}